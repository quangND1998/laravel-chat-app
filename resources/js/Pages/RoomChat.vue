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
                <Contacts :users="users" />
              </div>

              <!-- Right -->
              <PrivateChat />
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
        console.log("typing", user);
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
        console.log(e);
        this.users.map(user => {
          if (user.id == e.userId) {
            user.state = e.state;
          }
        });
      });
  },

  methods: {
  
    sendMessage() {
      let message = this.message;
      this.message = "";

      axios
        .post(this.route("send.message"), { room: this.room, message })
        .then(() => {
          //
        });
    },

    sendIcon(icon) {
      let message = `<i class="${icon}"></i>`;
      this.message = "";

      axios
        .post(this.route("send.message"), { room: this.room, message })
        .then(() => {
          //
        });
    },
    sendTypingEvent() {
      // console.log(this.room)
      Echo.join(`room.${this.room}`).whisper("typing", this.user);
    },
    changeState(state) {
      Echo.join(`room.${this.room}`).whisper("changeState", {
        state,
        userId: this.user.id
      });
    },

    getMessages (room) {
        const response =  axios.get(`/messages/list?room=${room}`)

    console.log('response',response)
    //   try {
      
    //     if (room.toString().includes('__')) {
    //       this.privateMessages = response.data
    //     //   this.scrollToBottom(document.getElementById('private_room'), false)
    //     } else {
    //         console.log(response)
    //       this.publicMessages = response.data
    //     //   this.scrollToBottom(document.getElementById('shared_room'), false)
    //     }
    //   } catch (error) {
    //     console.log(error)
    //   }
    },
    
  }
};
</script>
