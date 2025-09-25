<template>
  <!-- Cart Sidebar Overlay -->
  <div v-if="isOpen" class="cart-overlay" @click="close"></div>
  
  <!-- Cart Sidebar -->
  <div class="cart-sidebar" :class="{ open: isOpen }">
    <div class="cart-header">
      <h5 class="mb-0">
        <i class="fa-solid fa-cart-shopping me-2"></i>
        My Cart
        <span class="badge bg-primary ms-2">{{ items.length }}</span>
      </h5>
      <button class="btn-close" @click="close"></button>
    </div>

    <div class="cart-body">
      <div v-if="items.length === 0" class="empty-cart text-center py-4">
        <i class="fa-regular fa-cart-shopping fa-3x text-muted mb-3"></i>
        <p class="text-muted">Your cart is empty</p>
      </div>

      <div v-else class="cart-items">
        <div v-for="item in items" :key="item.id" class="cart-item">
          <div class="item-image">
            <img :src="item.image || '/images/placeholder-product.jpg'" :alt="item.name" />
          </div>
          <div class="item-details">
            <div class="item-name">{{ item.name }}</div>
            <div class="item-price">EGP {{ Number(item.price).toLocaleString() }}</div>
            <div class="item-controls">
              <button class="qty-btn" @click="decreaseQty(item)">-</button>
              <span class="qty">{{ item.qty }}</span>
              <button class="qty-btn" @click="increaseQty(item)">+</button>
            </div>
          </div>
          <button class="remove-btn" @click="removeItem(item)" title="Remove">
            <i class="fa-regular fa-trash-can"></i>
          </button>
        </div>
      </div>
    </div>

    <div v-if="items.length > 0" class="cart-footer">
      <div class="cart-summary">
        <div class="summary-row">
          <span>Subtotal</span>
          <span class="fw-bold">EGP {{ subtotal.toLocaleString() }}</span>
        </div>
        <div class="summary-row">
          <span>Shipping</span>
          <span>EGP 8</span>
        </div>
        <hr class="my-2">
        <div class="summary-row total">
          <span class="fw-bold">Total</span>
          <span class="fw-bold">EGP {{ (subtotal + 8).toLocaleString() }}</span>
        </div>
      </div>
      
      <button class="checkout-btn" @click="proceedToCheckout">
        Proceed to Checkout
        <i class="fa-solid fa-arrow-right ms-2"></i>
      </button>
    </div>
  </div>
</template>

<script>
import cart from '../../store/cart'

export default {
  name: 'CartSidebar',
  computed: {
    isOpen() { return cart.state.sidebarOpen },
    items() { return cart.state.items },
    subtotal() { return cart.total() }
  },
  methods: {
    close() { cart.closeSidebar() },
    increaseQty(item) {
      item.qty = (item.qty || 1) + 1
      this.saveCart()
    },
    decreaseQty(item) {
      if (item.qty > 1) {
        item.qty = item.qty - 1
        this.saveCart()
      }
    },
    removeItem(item) {
      cart.remove(item.id)
    },
    saveCart() {
      localStorage.setItem('sp_cart', JSON.stringify(cart.state.items))
    },
    proceedToCheckout() {
      this.close()
      this.$router.push('/checkout')
    }
  }
}
</script>

<style scoped>
.cart-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1040;
}

.cart-sidebar {
  position: fixed;
  top: 0;
  right: -400px;
  width: 400px;
  height: 100vh;
  background: white;
  box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
  z-index: 1050;
  transition: right 0.3s ease;
  display: flex;
  flex-direction: column;
}

.cart-sidebar.open {
  right: 0;
}

.cart-header {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: between;
  align-items: center;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 0.25rem;
  margin-left: auto;
}

.cart-body {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
}

.cart-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.item-image {
  width: 60px;
  height: 60px;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 6px;
}

.item-details {
  flex: 1;
}

.item-name {
  font-weight: 600;
  font-size: 0.9rem;
  line-height: 1.2;
  margin-bottom: 0.25rem;
}

.item-price {
  color: #ff7a00;
  font-weight: 700;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.item-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.qty-btn {
  width: 24px;
  height: 24px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 0.8rem;
}

.qty-btn:hover {
  background: #f5f5f5;
}

.qty {
  font-weight: 600;
  min-width: 20px;
  text-align: center;
  font-size: 0.9rem;
}

.remove-btn {
  background: none;
  border: none;
  color: #dc3545;
  cursor: pointer;
  padding: 0.25rem;
}

.remove-btn:hover {
  color: #c82333;
}

.cart-footer {
  border-top: 1px solid #eee;
  padding: 1rem;
}

.cart-summary {
  margin-bottom: 1rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.summary-row.total {
  font-size: 1.1rem;
  color: #333;
}

.checkout-btn {
  width: 100%;
  background: #ff7a00;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.checkout-btn:hover {
  background: #e66a00;
}

@media (max-width: 768px) {
  .cart-sidebar {
    width: 100vw;
    right: -100vw;
  }
}
</style>
