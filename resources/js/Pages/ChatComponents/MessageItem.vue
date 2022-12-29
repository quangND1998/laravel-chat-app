<template>
    <!-- Send -->
  <div class="flex justify-end mb-2 relative" v-if="message.sender.id == this.$page.props.auth.user.id"  >

    <div class="msg-actions flex mr-2">
      <div class="flex align-center">
        <i
          class="fa-regular fa-face-grin"
          data-toggle="tooltip"
          data-placement="top"
          title="React"
          @click="showEmoji"
        ></i>
      </div>
      
    </div>
    <div class="rounded py-2 px-3" style="background-color: #E2F7CB">
      <p class="text-sm mt-1">{{ message.content }}</p>
      <p class="text-right text-xs text-grey-dark mt-1">{{ formDate(message.created_at) }}</p>
    </div>
    <div
        class="msg_container_send absolute "
        data-toggle="tooltip"
        data-placement="top"
        :title="message.created_at"
      >
        <Reaction v-if="message.reactions.length" :reactions="message.reactions" />
      </div>
  </div>
    <!-- Recive -->
  <div class="flex mb-2 relative" v-else>
    <div class="rounded py-2 px-3" style="background-color: #F2F2F2">
      <p class="text-sm mt-1">{{ message.content }}</p>
      <p class="text-right text-xs text-grey-dark mt-1">{{ formDate(message.created_at) }}</p>
    </div>
    <div
        class="msg_container_send absolute  "
        data-toggle="tooltip"
        data-placement="top"
        :title="message.created_at"
      >
        <Reaction v-if="message.reactions.length" :reactions="message.reactions" />
      </div>
    <div class="msg-actions flex mr-2">
      <div class="flex align-center">
        <i
          class="fa-regular fa-face-grin"
          data-toggle="tooltip"
          data-placement="top"
          title="React"
          @click="showEmoji"
        ></i>
      </div>

    
    </div>
  </div>
</template>

<script>
import Reaction from "./Reaction";
import filter from "@/mixins/filter";
export default {
  mixins: [filter],
  props: {
    message: Object
  },
  components: {
    Reaction
  },
  mounted() {
    this.$nextTick(() => {
      $('[data-toggle="tooltip"]').tooltip();
    });
  },
  methods: {
    showEmoji(event) {
      console.log(event);
      this.$emit("showEmoji", this.message, event);
    }
  }
};
</script>

<style >
</style>