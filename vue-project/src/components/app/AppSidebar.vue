<script setup lang='ts'>
import { useNavigationMenu } from '@/composables/navigation'
import { stateStore } from '@/store/state'

const version = ref(import.meta.env.VITE_APP_VERSION)

const { menu } = useNavigationMenu()

function onResize() {
  if (window.innerWidth <= 980) {
    stateStore.setCollapsed(true)
    stateStore.setIsOnMobile(true)
  }
  else {
    stateStore.setCollapsed(false)
    stateStore.setIsOnMobile(false)
  }
}

function onToggleCollapse() {
}

function onItemClick() {
}

onMounted(() => {
  onResize()
  window.addEventListener('resize', onResize)
})
</script>

<template>
  <div>
    <sidebar-menu
      v-model:collapsed="stateStore.collapsed"
      :menu="menu"
      :show-one-child="true"
      width="200px"
      width-collapsed="60px"
      @update:collapsed="onToggleCollapse"
      @item-click="onItemClick"
    >
      <template #header>
        <div v-if="!stateStore.collapsed" class="place-items-center">
          <img class="m-2 size-16" src="/public/img/iHOMIS+.png" alt="iHOMIS+">
        </div>
        <div v-else>
          <img class="size-16" src="/public/img/iHOMIS+.png" alt="iHOMIS+">
        </div>
      </template>
      <template #footer>
        <div class="m-2 text-center text-xs text-primary">
          <span v-if="!stateStore.collapsed">iHOMIS+ {{ version }}</span>
          <span v-if="stateStore.collapsed">{{ version }}</span>
        </div>
      </template>
    </sidebar-menu>
    <div
      v-if="stateStore.isOnMobile && !stateStore.collapsed"
      class="sidebar-overlay"
      @click="stateStore.collapsed = true"
    />
  </div>
</template>

<style lang="scss">
.sidebar-overlay {
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-color: #000;
  opacity: 0.5;
  z-index: 900;
}
</style>
