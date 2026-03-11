<script setup lang="ts">
import { RouterLink, RouterView } from "vue-router";
</script>

<template>
  <div class="navbar bg-gray-900">
    <div class="navbar-start"></div>
    <div class="navbar-center">
      <a class="btn btn-ghost normal-case text-xl hover:no-underline">ChatApp - WebAppDev @ BCIT</a>
    </div>
    <div class="navbar-end">
      <a href="/" class="btn btn-ghost normal-case text-xl hover:no-underline">Chat</a>
      <a href="/posts" class="btn btn-ghost normal-case text-xl hover:no-underline">Posts</a>
    </div>
  </div>
  <RouterView />
</template>

<style scoped></style>
