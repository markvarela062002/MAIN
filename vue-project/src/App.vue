<script setup lang='ts'>
useHead({
  title: 'iHOMIS+',
  meta: [
    {
      name: 'description',
      content: 'Integrated Hospital Operations and Management Information Systems Plus',
    },
  ],
})

import { defineComponent } from "vue";

defineComponent({
	name: "App",
});
</script>

<template>
  <div id="bar" />
  <router-view />
</template>

<style>
@import "@/assets/tailwind.css";
</style>

<style lang='scss'>
@use 'App.scss';
</style>
