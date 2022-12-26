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
                <Contacts :users="users" @selectReceiver="selectReceiver" />
              </div>

              <!-- Right -->
              <PrivateChat v-if="privateChat.selectedReceiver" :privateChat="privateChat" :messages="privateMessages"
                @closePrivateChat="closePrivateChat" @saveMessage="saveMessage"
                @focusPrivateInput="focusPrivateInput" />
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

  props: ["room", "link", "user"],

  data() {
    return {

      users: [],
      message: "",
      UserTyping: false,
      typingTimer: false,
      publicMessages: [],
      privateMessages: [],
      privateChat: {
        selectedReceiver: null,
        isPrivateChatExpand: false,
        isSelectedReceiverTyping: false,
        hasNewMessage: false,
        isSeen: null, // null: no new message, false: a message is waiting to be seen, true: user seen message (should display "Seen at..")
        seenAt: '',
        roomId: ''
      }
    };
  },

  mounted() {

    this.getMessages(this.room)
    Echo.join(`room.${this.room}`)
      .here(users => {
        this.users = users;

      })
      .joining(user => {
        console.log(user);
        this.users.push(user);

      })
      .leaving(user => {
        this.users.splice(this.users.indexOf(user), 1);

      })
      .listen("Message", ({ message, user }) => {

      })
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
        .listen('Message', e => {
          if (this.privateChat.selectedReceiver && e.message.sender.id === this.privateChat.selectedReceiver.id) {
            this.privateMessages.push(e.message)
            this.privateChat.isSeen = null // when receive new private message, considered user have seen -> reset isSeen to inital state
            this.privateChat.hasNewMessage = true // notify user there's new message
            this.scrollToBottom(document.getElementById('private_room'), true)
          } else { // if private chat window doens't open, then we set the badge in ListUser
            const index = this.users.findIndex(item => item.id === e.message.sender.id)
            if (index > -1) {
              this.users[index].new_messages++
            }
          }
        })
    
  },
  computed: {
    totalUnreadPrivateMessages () {
      let count = 0
      this.users.forEach(item => {
        count += item.new_messages
      })
      return count
    }
  },
  watch: {
    totalUnreadPrivateMessages () {
      if (this.totalUnreadPrivateMessages > 0) {
        document.title = `${this.totalUnreadPrivateMessages > 0 ? '(' + this.totalUnreadPrivateMessages + ')' : ''} - ${this.$root.appName}`
      } else {
        document.title = this.$page.props.auth.appName
      }
    }
  },
  methods: {
    async getMessages(room) {

      const response = await axios.get(`/messages/list?room=${room}`)
      try {

        if (room.toString().includes('__')) {
          this.privateMessages = response.data
            this.scrollToBottom(document.getElementById('private_room'), false)
        } else {
          console.log(response)
          this.publicMessages = response.data
            this.scrollToBottom(document.getElementById('shared_room'), false)
        }
      } catch (error) {
        console.log(error)
      }
    },

    async saveMessage (message, receiver = null) {
      try {
        if ((!receiver && !message.trim().length)) {
          return
        }
        const response = await axios.post('/messages/post', {
          receiver,
          content: message,
          room: receiver ? null : this.currentRoom.id
        })
        if (receiver) {
          console.log('receiver',receiver)
          console.log('saveMessage_receiver',response.data.message)
          this.privateMessages.push(response.data.message)
          this.privateChat.isSeen = false // waiting for other to seen this message
          // send message indicate that user stop typing (incase Throttle function isn't called)
          Echo.private(`room.${this.privateChat.roomId}`)
            .whisper('typing', {
              user: this.$page.props.auth.user,
              isTyping: false
            })
        } else {
          console.log('saveMessage',response.data.message)
          this.publicMessages.push(response.data.message)
        }
        this.scrollToBottom(document.getElementById(`${receiver ? 'private' : 'shared'}_room`), true)
      } catch (error) {
        console.log(error)
      }
    },
    async selectReceiver(receiver) {
      console.log(receiver)
      if (this.$page.props.auth.user.id === receiver.id) {
        return
      }
      const roomId = this.$page.props.auth.user.id > receiver.id ? `${receiver.id}__${this.$page.props.auth.user.id}` : `${this.$page.props.auth.user.id}__${receiver.id}`
      this.privateChat.selectedReceiver = receiver
      this.privateChat.isPrivateChatExpand = true
      this.privateChat.roomId = roomId
      Echo.private(`room.${roomId}`) // this room to receive whisper events
        .listenForWhisper('typing', (e) => {
          this.privateChat.isSelectedReceiverTyping = e.isTyping
          this.scrollToBottom(document.getElementById('private_room'), true)
        })
        .listenForWhisper('seen', (e) => {
          if (this.privateChat.isSeen === false) { // check if user waiting for his message to be seen
            this.privateChat.isSeen = true
            this.privateChat.seenAt = e.time
            this.scrollToBottom(document.getElementById('private_room'), true)
          }
        })
      await this.getMessages(roomId) // need to await until messages are loaded first then we are able to focus the input below
    },
    closePrivateChat () {
      this.privateChat.selectedReceiver = null
      this.privateChat.isPrivateChatExpand = false
    },
    scrollToBottom (element, animate = true) {
      if (!element) {
        return
      }
      this.$nextTick(() => { // run after Vue finishes updating the DOM
        if (animate) {
          $(element).animate(
            { scrollTop: element.scrollHeight },
            { duration: 'medium', easing: 'swing' }
          )
        } else {
          $(element).scrollTop(element.scrollHeight)
        }
      })
    },
    focusPrivateInput () {
      const input = document.getElementById('private_input')
      console.log(input)
      if (input) { // incase we toggle private chat then this input will be removed
        input.focus()
        Echo.private(`room.${this.privateChat.roomId}`)
          .whisper('seen', {
            user: this.$page.props.auth.user,
            seen: true,
            time: new Date()
          })
        this.privateChat.hasNewMessage = false // set this to false as now user is focusing the chat
        const index = this.users.findIndex(item => item.id === this.privateChat.selectedReceiver.id)
        if (index > -1) {
          this.users[index].new_messages = 0
        }
      }
    }

  },
  beforeDestroy () {
    if (this.selectedReceiver) { // leave private chat if current has
      Echo.leave(`room.${this.privateChat.roomId}`)
    }
    Echo.leave(`room.${this.currentRoom.id}`) // leave the shared room
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
  0%, 100% {
    background: white;
  }
  50% {
    background: #2e7fd7;
  }
}
</style>