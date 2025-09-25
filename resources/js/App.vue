<template>
  <div id="app">
    <NavbarLayout />
    <router-view />
    <FooterLayout />
    <CartSidebar />
  </div>
</template>

<script>
import NavbarLayout from './components/layouts/Navbar.vue'
import FooterLayout from './components/layouts/Footer.vue'
import CartSidebar from './components/layouts/CartSidebar.vue'

export default {
  name: 'App',
  components: { NavbarLayout, FooterLayout, CartSidebar }
}
</script>
