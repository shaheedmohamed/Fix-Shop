<template>
  <div class="container py-4">
    <h2 class="mb-3">My Cart</h2>
    <div v-if="items.length===0" class="alert alert-light">Your cart is empty.</div>
    <div v-else class="row g-3">
      <div v-for="it in items" :key="it.id" class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body d-flex gap-3 align-items-center">
            <img v-if="it.image" :src="it.image" alt="" style="width:80px;height:80px;object-fit:contain" />
            <div class="flex-grow-1">
              <div class="fw-bold">{{ it.name }}</div>
              <div class="text-muted small">EGP {{ Number(it.price).toLocaleString() }}</div>
              <div class="mt-2 d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary" @click="dec(it)">-</button>
                <span class="mx-2">{{ it.qty }}</span>
                <button class="btn btn-sm btn-outline-secondary" @click="inc(it)">+</button>
                <button class="btn btn-sm btn-outline-danger ms-3" @click="remove(it)"><i class="fa-regular fa-trash-can me-1"></i>Remove</button>
              </div>
            </div>
            <div class="fw-bold">EGP {{ (it.price*it.qty).toLocaleString() }}</div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div class="fw-bold">Total: EGP {{ total.toLocaleString() }}</div>
            <button class="btn btn-primary" @click="checkout">Checkout</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import cart from '../../store/cart'
export default {
  name: 'CartPage',
  computed:{
    items(){ return cart.state.items },
    total(){ return cart.total() }
  },
  methods:{
    inc(it){ it.qty = (it.qty||1)+1; localStorage.setItem('sp_cart', JSON.stringify(cart.state.items)) },
    dec(it){ it.qty = Math.max(1,(it.qty||1)-1); localStorage.setItem('sp_cart', JSON.stringify(cart.state.items)) },
    remove(it){ cart.remove(it.id) },
    checkout(){ alert('Checkout coming soon') }
  }
}
</script>
