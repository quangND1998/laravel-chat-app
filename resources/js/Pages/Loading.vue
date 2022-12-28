<script setup>
  import { ref } from "vue";
  import InfiniteLoading from "v3-infinite-loading";
  import "v3-infinite-loading/lib/style.css";

  let comments = ref([]);
  let page = 1;
  let top = true;
  const load = async $state => {
    console.log("loading...");

    try {
      const response = await fetch(
        "/messages/list?room=1__2&page=" + page
      );
      const json = await response.json();
      console.log(json)
      if (json.data.length < 10) $state.complete();
      else {
        comments.value.unshift(...json.data.reverse());
        $state.loaded();
      }
      page++;
    } catch (error) {
      $state.error();
    }
  };
</script>
<template>
    <div>
      <InfiniteLoading @infinite="load"   top />
    <div class="result" v-for="comment in comments" :key="comment.id">
      <div>{{ comment.content }}</div>
      <div>{{ comment.id }}</div>
    </div>
    </div>
    
</template>

<style>
  #app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin-top: 60px;
  }

  .result {
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-weight: 300;
    width: 400px;
    padding: 10px;
    text-align: center;
    margin: 0 auto 10px auto;
    background: #eceef0;
    border-radius: 10px;
  }
</style>