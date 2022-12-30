<template>
  <!-- Send -->

  <div
    class="flex justify-end mb-2  " 
    v-if="message.sender.id == this.$page.props.auth.user.id"
  >
    <div class="msg-actions flex mr-2 ">
      <div class="flex items-center">
        <i
          class="fa-regular fa-face-grin "
          data-toggle="tooltip"
          data-placement="top"
          title="React"
          @click="showEmoji"
        ></i>
      </div>
    </div>
    <div
      class="rounded py-2 px-3 relative"
      style="background-color: #E2F7CB"
      data-toggle="tooltip"
      data-placement="left"
      :title="formDate(message.created_at)"
    >
      <p class="text-sm mt-1">{{ message.content }}</p>
      <div
      class="msg_container_send absolute left-0"
   
    >
      <Reaction v-if="message.reactions.length" :reactions="message.reactions" />
    </div>
      <!-- <p class="text-right text-xs text-grey-dark mt-1">{{ formDate(message.created_at) }}</p> -->
    </div>
  
  </div>
  <!-- Recive -->
  <div class="flex mb-2 "  v-else>
    <div class="rounded py-2 px-3 relative"   data-toggle="tooltip"
      data-placement="bottom"
      :title="formDate(message.created_at)" style="background-color: #F2F2F2">
      <p class="text-sm mt-1">{{ message.content }}</p>
      <div
      class="msg_container_send absolute  right-0" 

    >
      <Reaction v-if="message.reactions.length" :reactions="message.reactions" />
    </div>
      <!-- <p class="text-right text-xs text-grey-dark mt-1">{{ formDate(message.created_at) }}</p> -->
    </div>

    <div class="msg-actions flex ml-2 items-center ">
      <div class="flex items-center">
        <i
          class="fa-regular fa-face-grin " 
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
  emits: ["showEmoji"],
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

<style  scoped>

</style> >
