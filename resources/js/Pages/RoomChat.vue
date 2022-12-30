<template>
  <div>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
      <div>
        <div class="container mx-auto" style="margin-top: -30px;">
          <div class="py-6 h-screen">
            <div class="flex border border-grey rounded shadow-lg h-full">
              <!-- Left -->
              <div class="w-1/3 border flex flex-col">
                <!-- Header -->
                <HeaderLeft></HeaderLeft>

                <!-- Search -->

                <!-- Contacts -->
                <Contacts
                  :users="users"
                  @selectReceiver="selectReceiver"
                  :userSelected="privateChat.selectedReceiver"
                />
              </div>

              <!-- Right -->
              <div class="w-2/3 border flex flex-col">
                <HeaderRight
                  v-if="privateChat.selectedReceiver"
                  :user="privateChat.selectedReceiver"
                  @closePrivateChat="closePrivateChat"
                />
                <PrivateChat
                  v-if="privateChat.selectedReceiver"
                  :privateChat="privateChat"
                  :selectedMessage="selectedMessage"
                  :isShowEmoji="isShowEmoji"
                  :emojiCoordinates="emojiCoordinates"
                  @saveMessage="saveMessage"
                  @focusPrivateInput="focusPrivateInput"
                  @showEmoji="showEmoji"
                  @hideEmoji="hideEmoji"
                  @selectEmoji="selectEmoji"
                  @getMessages="getMessages"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </BreezeAuthenticatedLayout>
  </div>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import BreezeInput from "@/Components/Input.vue";
import { Head } from "@inertiajs/inertia-vue3";
import HeaderLeft from "@/Pages/ChatComponents/HeaderLeft";
import HeaderRight from "@/Pages/ChatComponents/HeaderRight";
import Contacts from "@/Pages/ChatComponents/Contacts";
import PrivateChat from "@/Pages/ChatComponents/PrivateChat";
import sanitizeHtml from 'sanitize-html'
export default {
  components: {
    BreezeAuthenticatedLayout,
    BreezeInput,
    Head,
    HeaderLeft,
    HeaderRight,
    Contacts,
    PrivateChat
  },
  props: ["room", "link", "user",'emojis'],
  data() {
    return {
      users: [],
      message: "",
      UserTyping: false,
      typingTimer: false,
      publicChat: {
        message: {
          isLoading: false,
          list: [],
          currentPage: 0,
          perPage: 0,
          total: 0,
          lastPage: 0,
          newMessageArrived: 0 // number of new messages we just got (use for saving scroll position)
        }
      },
      privateChat: {
        selectedReceiver: null,
        isPrivateChatExpand: false,
        isSelectedReceiverTyping: false,
        hasNewMessage: false,
        isSeen: null, // null: no new message, false: a message is waiting to be seen, true: user seen message (should display "Seen at..")
        seenAt: "",
        roomId: "",
        message: {
          isLoading: false,
          list: [],
          currentPage: 0,
          perPage: 0,
          total: 0,
          lastPage: 0,
          newMessageArrived: 0 // number of new messages we just got (use for saving scroll position)
        }
      },
      emojiCoordinates: {
        x: 0,
        y: 0
      },
      isShowEmoji: false,
      selectedMessage: null
    };
  },
  mounted() {
    this.getMessages(this.room);
    Echo.join(`room.${this.room}`)
      .here(users => {
        this.users = users;
      })
      .joining(user => {
        // console.log(user);
        this.users.push(user);
      })
      .leaving(user => {
        this.users.splice(this.users.indexOf(user), 1);
      })
      .listen("Message", ({ message, user }) => {})
      .listenForWhisper("typing", user => {
        // console.log("typing", user);
        this.UserTyping = user;
        if (this.typingTimer) {
          clearTimeout(this.typingTimer);
        }
        this.typingTimer = setTimeout(() => {
          this.UserTyping = false;
        }, 2000);
        // console.log(this.isSeen)
      })
      .listenForWhisper("changeState", e => {
        // console.log(e);
        this.users.map(user => {
          if (user.id == e.userId) {
            user.state = e.state;
          }
        });
      });
    Echo.private(`room.${this.$page.props.auth.user.id}`) // listen to user's own room (in order to receive all private messages from other users)
      .listen("Message", e => {
        if (
          this.privateChat.selectedReceiver &&
          e.message.sender.id === this.privateChat.selectedReceiver.id
        ) {
          this.privateChat.message.list.push(e.message)
          this.privateChat.isSeen = null; // when receive new private message, considered user have seen -> reset isSeen to inital state
          this.privateChat.hasNewMessage = true; // notify user there's new message
          this.scrollToBottom(document.getElementById("private_room"), true);
        } else {
          // if private chat window doens't open, then we set the badge in ListUser
          const index = this.users.findIndex(
            item => item.id === e.message.sender.id
          );
          if (index > -1) {
            this.users[index].new_messages++;
          }
        }
      })

  },
  computed: {
    totalUnreadPrivateMessages() {
      let count = 0;
      this.users.forEach(item => {
        count += item.new_messages;
      });
      return count;
    }
  },
  watch: {
    totalUnreadPrivateMessages() {
      if (this.totalUnreadPrivateMessages > 0) {
        document.title = `${
          this.totalUnreadPrivateMessages > 0
            ? "(" + this.totalUnreadPrivateMessages + ")"
            : ""
        } - ${this.$root.appName}`;
      } else {
        document.title = this.$page.props.auth.appName;
      }
    }
  },
  methods: {
    async getMessages(room, page = 1, loadMore = false) {
      const isPrivate = room.toString().includes("__");
      const chat = isPrivate ? this.privateChat : this.publicChat;
      // console.log(room)
      try {
        chat.message.isLoading = true;
        const response = await axios.get(
          `/messages/list?room=${room}&page=${page}`
        );
        // console.log(response)
        chat.message.list = [
          ...response.data.data.reverse(),
          ...chat.message.list
        ];
        chat.message.currentPage = response.data.current_page;
        chat.message.perPage = response.data.per_page;
        chat.message.lastPage = response.data.last_page;
        chat.message.total = response.data.total;
        chat.message.newMessageArrived = response.data.data.length;
        if (loadMore) {
          this.$nextTick(() => {
            const el = $(isPrivate ? "#private_room" : "#shared_room");
            const lastFirstMessage = el
              .children()
              .eq(chat.message.newMessageArrived - 1);
            el.scrollTop(lastFirstMessage.position().top - 10);
          });
        } else {
          this.scrollToBottom(
            document.getElementById(isPrivate ? "private_room" : "shared_room"),
            false
          );
        }
      } catch (error) {
        // console.log(error);
      } finally {
        chat.message.isLoading = false;
      }
    },
    async saveMessage(message, receiver = null) {
      try {
        if (!receiver && !message.trim().length) {
          return;
        }

                // clean data before save to DB
        message = sanitizeHtml(message).trim()
        if (!message.length) {
          return
        }
        const response = await axios.post("/messages/post", {
          receiver,
          content: message,
          room: receiver ? null : this.currentRoom.id
        });
        if (receiver) {
          // console.log('receiver',receiver)
          // console.log('saveMessage_receiver',response.data.message)
          this.privateChat.message.list.push(response.data.message)
          this.privateChat.isSeen = false // waiting for other to seen this message
          // send message indicate that user stop typing (incase Throttle function isn't called)
          Echo.private(`room.${this.privateChat.roomId}`).whisper("typing", {
            user: this.$page.props.auth.user,
            isTyping: false
          });
        } else {
          // console.log('saveMessage',response.data.message)
          this.publicChat.message.list.push(response.data.message)
        }
        this.scrollToBottom(
          document.getElementById(`${receiver ? "private" : "shared"}_room`),
          true
        );
      } catch (error) {
        console.log(error);
      }
    },
    async selectReceiver(receiver) {
      // console.log(receiver)
      if (this.$page.props.auth.user.id === receiver.id) {
        return;
      }
      this.resetPrivateChat()
      const roomId =
        this.$page.props.auth.user.id > receiver.id
          ? `${receiver.id}__${this.$page.props.auth.user.id}`
          : `${this.$page.props.auth.user.id}__${receiver.id}`;
      this.privateChat.selectedReceiver = receiver;
      this.privateChat.isPrivateChatExpand = true;
      this.privateChat.roomId = roomId;
      Echo.private(`room.${roomId}`) // this room to receive whisper events
        .listenForWhisper("typing", e => {
      
          this.privateChat.isSelectedReceiverTyping = e.isTyping;
          this.scrollToBottom(document.getElementById("private_room"), true);
        })
        .listenForWhisper("seen", e => {
          // console.log(e);
          if (this.privateChat.isSeen === false) {
            // check if user waiting for his message to be seen
            this.privateChat.isSeen = true;
            this.privateChat.seenAt = e.time;
            this.scrollToBottom(document.getElementById("private_room"), true);
          }
        }).listen('MessageReacted', e => {
          // console.log(e)
          this.onOtherUserReaction(e.message, 'private')
        })
      await this.getMessages(roomId); // need to await until messages are loaded first then we are able to focus the input below
    },
    closePrivateChat() {
      this.resetPrivateChat()
    },
    scrollToBottom(element, animate = true) {
      if (!element) {
        return;
      }
      this.$nextTick(() => {
        // run after Vue finishes updating the DOM
        if (animate) {
          $(element).animate(
            { scrollTop: element.scrollHeight },
            { duration: "medium", easing: "swing" }
          );
        } else {
          $(element).scrollTop(element.scrollHeight);
        }
      });
    },
    focusPrivateInput() {
      const input = document.getElementById("private_input");
      // console.log(input)
      if (input) {
        // incase we toggle private chat then this input will be removed
        input.focus();
        Echo.private(`room.${this.privateChat.roomId}`).whisper("seen", {
          user: this.$page.props.auth.user,
          seen: true,
          time: new Date()
        });
        this.privateChat.hasNewMessage = false; // set this to false as now user is focusing the chat
        const index = this.users.findIndex(
          item => item.id === this.privateChat.selectedReceiver.id
        );
        if (index > -1) {
          this.users[index].new_messages = 0;
        }
      }
    },
    showEmoji (message, event) {
      // console.log('showEmoji',message)
      this.emojiCoordinates.x = event.clientX
      this.emojiCoordinates.y = event.clientY
      this.isShowEmoji = true
      this.selectedMessage = message
    },
    hideEmoji () {
      this.isShowEmoji = false
      this.selectedMessage = null
    },
    resetPrivateChat () { // reset private chat when change to another user
      this.privateChat.message.list = []
      this.privateChat.selectedReceiver = null
      this.privateChat.isPrivateChatExpand = false
      this.privateChat.isSelectedReceiverTyping = false
      this.privateChat.hasNewMessage = false
      this.privateChat.isSeen = null // null: no new message, false: a message is waiting to be seen, true: user seen message (should display "Seen at..")
      this.privateChat.seenAt = ''
      this.privateChat.roomId = ''
      this.privateChat.isOnline = true
    },
    async selectEmoji (emoji) {
      
      try {
        const response = await axios.post('/reactions/post', {
          msg_id: this.selectedMessage.id,
          user_id: this.$page.props.auth.user.id,
          emoji_id: emoji.id
        })

        // console.log(response.data.message.reactions)
        const index = this.selectedMessage.reactions.findIndex(item => item.user_id === this.$page.props.auth.user.id)
        if (index > -1) {

          const reaction = this.selectedMessage.reactions[index]
          if (emoji.id === reaction.emoji_id) { // deactive
            this.selectedMessage.reactions.splice(index, 1)
          } else {
            reaction.emoji_id = emoji.id
          }
        } else { // user first react
          // const { reaction } = response.data.reactions
          this.selectedMessage.reactions =response.data.message.reactions;
        }
        this.hideEmoji()
      } catch (error) {
        console.log(error)
      }
    },
    onOtherUserReaction (message_reaction, room) {
      // console.log(message_reaction)
      let listMessage = []
      listMessage = room === 'share' ? this.publicChat.message.list : this.privateChat.message.list
      const messageIndex = listMessage.findIndex(m => m.id === message_reaction.id)
      if (messageIndex > -1) {
        const message = listMessage[messageIndex]
        message.reactions = message_reaction.reactions
      }
    }
  },
  beforeDestroy() {
    if (this.selectedReceiver) {
      // leave private chat if current has
      Echo.leave(`room.${this.privateChat.roomId}`);
    }
    Echo.leave(`room.${this.currentRoom.id}`); // leave the shared room
  }
};
</script>
<style lang="scss">
@keyframes wave {
  0%,
  60%,
  100% {
    transform: initial;
  }

  30% {
    transform: translateY(-15px);
  }
}

#wave {
  .dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-right: 2px;
    background: white;
    animation: wave 1.3s linear infinite;

    &:nth-child(2) {
      animation-delay: -1.1s;
    }

    &:nth-child(3) {
      animation-delay: -0.9s;
    }
  }
}

.blink-anim {
  animation: blink 2s infinite;
}

@keyframes wave {
  0%,
  60%,
  100% {
    transform: initial;
  }

  30% {
    transform: translateY(-7px);
  }
}

@keyframes blink {
  0%,
  100% {
    background: white;
  }

  50% {
    background: #2e7fd7;
  }
}
</style>