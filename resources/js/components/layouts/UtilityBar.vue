<template>
  <div class="utility-bar d-none d-lg-block">
    <div class="container d-flex justify-content-end gap-3 py-1 small">
      <RouterLink to="/contact" class="link-underline">
        <i class="fa-regular fa-envelope me-1"></i> Contact
      </RouterLink>
      <button class="btn btn-link p-0 link-underline" @click="messageAdmin">
        <i class="fa-regular fa-message me-1"></i> Support Team
      </button>
    </div>
  </div>
</template>

<script>
import messages from '../../store/messages'
export default {
  name: 'UtilityBar',
  methods: {
    async messageAdmin(){
      try {
        const id = await messages.startConversationWithAdmin('Support request: Hello! I need help.')
        if (id) this.$router.push({ name: 'messages', query: { c: id } })
      } catch (_) {
        this.$router.push({ name: 'messages' })
      }
    }
  }
}
</script>

<style scoped>
.utility-bar { background: #0b2239; color: #cfe0f2; }
.link-underline { color: #cfe0f2; text-decoration: none; }
.link-underline:hover { text-decoration: underline; color: #ffffff; }
</style>
