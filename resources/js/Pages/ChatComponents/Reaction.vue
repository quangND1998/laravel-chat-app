<template>
    <div class=" flex items-center bg-white shadow-sm rounded-2xl  px-1 justify-end">
      <div
        class="flex ml-1"
        v-for="e in reactionFormat"
        :key="e.id"
      >
        <img :src="e.src" :alt="e.alt" class="w-3 h-3">
      </div>
      <div class=" ml-1 flex">
        <span class="total ml-1 text-blue-500 text-xs font-semibold  ">
          {{ reactions.length }}
        </span>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: ['reactions'],
    computed: {
      reactionFormat () {
        const listReactionEmojis = []
        for (const e of this.$page.props.emojis) {
          const index = this.reactions.findIndex(r => r.emoji_id === e.id)
          if (index > -1) {
            const indexInList = listReactionEmojis.findIndex(item => item.id === e.id)
            if (indexInList === -1) {
              listReactionEmojis.push(e)
            }
          }
        }
        return listReactionEmojis
      }
    }
  }
  </script>
  
  <style lang="scss">
  .reaction-container {
    box-shadow: 0 2px 4px rgba(0, 0, 0, .15);
    background: #fff;
    border-radius: 15px;
    position: absolute;
    right: 0;
    padding: 0 4px;
    .reaction-item {
      img {
        width: 14px;
        height: 14px;
      }
      .total {
        color: #0084ff;
        font-weight: 600;
        font-size: 13px;
      }
    }
    .reaction-item:not(:first-child):not(:last-child) {
      margin-left: 4px;
    }
  }
  </style>