<template>

  <div class="flex-1 overflow-auto" style="background-color: #DAD3CC" id="private_room"
    :class="privateChat.hasNewMessage ? 'blink-anim' : ''" >
    <div class="loading mb-2 text-center" v-if="privateChat.message.isLoading">
        <svg
          version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
          <path fill="#FF6700" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z" transform="rotate(18.3216 25 25)">
            <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform>
          </path>
        </svg>
      </div>
    
      <!-- <div class="flex justify-center mb-2">
          <div class="rounded py-2 px-4" style="background-color: #DDECF2">
            <p class="text-sm uppercase">February 20, 2018</p>
          </div>
        </div> -->
        <MessageItem v-for="(message, index) in privateChat.message.list" :key="index"  :message="message"
        @showEmoji="showEmoji" />



     
 

    <div class=" justify-end float-right" v-if="privateChat.isSeen">
      <i class="text-xs">Seen {{ localDate(privateChat.seenAt) }}</i>
    </div>
    <div class="d-flex justify-content-start mb-4" v-if="privateChat.isSelectedReceiverTyping">
      <div class="img_cont_msg">
        <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg" class="w-10 h-10 rounded-full">
      </div>
      <div class="msg_container">
        <div id="wave">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>
    </div>
    <!-- <span class="inline-block" v-if="privateChat.isSelectedReceiverTyping">
                        <img src="/images/typing.gif" class="w-16 " />
                        {{ privateChat.isSelectedReceiverTyping.name }} is Typing ...
      </span> -->
  
  </div>

  <!-- Input -->
  <div class="bg-grey-lighter px-4 py-4 flex items-center">
   <BreezeDropdown :display="'bottom'" >

    <template #trigger>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
        <path opacity=".45" fill="#263238"
          d="M9.153 11.603c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962zm-3.204 1.362c-.026-.307-.131 5.218 6.063 5.551 6.066-.25 6.066-5.551 6.066-5.551-6.078 1.416-12.129 0-12.129 0zm11.363 1.108s-.669 1.959-5.051 1.959c-3.505 0-5.388-1.164-5.607-1.959 0 0 5.912 1.055 10.658 0zM11.804 1.011C5.609 1.011.978 6.033.978 12.228s4.826 10.761 11.021 10.761S23.02 18.423 23.02 12.228c.001-6.195-5.021-11.217-11.216-11.217zM12 21.354c-5.273 0-9.381-3.886-9.381-9.159s3.942-9.548 9.215-9.548 9.548 4.275 9.548 9.548c-.001 5.272-4.109 9.159-9.382 9.159zm3.108-9.751c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962z" />
      </svg>
    </template>
    <template #content>

      <EmojiPicker :native="true" @select="addInputEmoji" />
    </template>
    </BreezeDropdown>
    <div class="flex-1 mx-4">
      <input class="w-full border rounded px-2 py-2" id="private_input" v-model="inputMessage" type="text"
        @keyup.enter="saveMessage" @input="onInputPrivateChange" @click="$emit('focusPrivateInput')" />
    </div>
    <div>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
        <path fill="#263238" fill-opacity="0.45"
          d="M11.999 14.942c2.001 0 3.531-1.53 3.531-3.531V4.35c0-2.001-1.53-3.531-3.531-3.531S8.469 2.35 8.469 4.35v7.061c0 2.001 1.53 3.531 3.53 3.531zm6.238-3.53c0 3.531-2.942 6.002-6.237 6.002s-6.237-2.471-6.237-6.002H3.761c0 4.001 3.178 7.297 7.061 7.885v3.884h2.354v-3.884c3.884-.588 7.061-3.884 7.061-7.885h-2z" />
      </svg>

    </div>


  </div>
  <Emoji
      :emojiCoordinates="emojiCoordinates"
      :isShow="isShowEmoji"
      :selectedMessage="selectedMessage"
      @hideEmoji="hideEmoji"
      @selectEmoji="selectEmoji"
    />


 
</template>

<script>
import HeaderRight from "@/Pages/ChatComponents/HeaderRight";
import { throttle } from 'lodash'
import filter from '@/mixins/filter'
import EmojiPicker from 'vue3-emoji-picker'
import BreezeDropdown from "@/Components/Dropdown.vue";
import Emoji from './Emoji'
import MessageItem from './MessageItem'
export default {
  mixins: [filter],
  components: {
    HeaderRight,
    EmojiPicker,
    BreezeDropdown,
    MessageItem,
    Emoji
  },
  props: {
    privateChat: Object,
    selectedMessage: {
      type: Object
    },
    isShowEmoji: {
      type: Boolean
    },
    emojiCoordinates: {
      type: Object
    }
  },
  data() {
    return {
      inputMessage: ''
    }
  },
  emits: ['saveMessage', 'focusPrivateInput','getMessages','showEmoji','hideEmoji','selectEmoji'],
  mounted() {
    this.$emit('focusPrivateInput')
    $('#private_room').on('scroll', async () => {
      const scroll = $('#private_room').scrollTop()
      if (scroll < 1 && this.privateChat.message.currentPage < this.privateChat.message.lastPage) {
        this.$emit('getMessages', this.privateChat.roomId, this.privateChat.message.currentPage + 1, true)
      }
    })
  },
  methods: {
    saveMessage() {
      this.$emit('saveMessage', this.inputMessage, this.privateChat.selectedReceiver.id)
      this.inputMessage = ''
    },
    onInputPrivateChange: throttle(function () {
      Echo.private(`room.${this.privateChat.roomId}`)
        .whisper('typing', {
          user: this.$page.props.auth.user,
          isTyping: this.inputMessage.length > 0
        })
    }, 2000), // keep tell other that we're typing because other user may close the private chat window then re open during we're typing

    addInputEmoji(emoji) {
      this.inputMessage += emoji.i
    },
    showEmoji (message, event) {
      this.$emit('showEmoji', message, event)
    },
    hideEmoji () {
      this.$emit('hideEmoji')
    },
    selectEmoji (emoji) {
      this.$emit('selectEmoji', emoji)
    },
  },

  beforeDestroy() {
    Echo.leave(`room.${this.privateChat.roomId}`)
  }
};
</script>

<style>
.private-message-container {
  z-index: 1;
}
</style>