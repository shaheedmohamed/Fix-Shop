<template>
  <aside class="filters card shadow-sm">
    <div class="card-header py-2 bg-primary text-white fw-semibold">
      Filters
    </div>
    <div class="card-body">
    <div class="mb-3">
      <div class="section-title">Category</div>
      <div v-if="loadingCats" class="text-muted small">Loading...</div>
      <template v-else>
        <div v-for="c in categories" :key="c.id" class="form-check">
          <input class="form-check-input" type="radio" :id="'cat'+c.id" name="cat" :value="c.id" v-model="local.category_id" @change="apply">
          <label class="form-check-label" :for="'cat'+c.id">{{ c.name }}</label>
        </div>
        <button v-if="local.category_id" class="btn btn-sm btn-link p-0 mt-1" @click="local.category_id=null; apply()">Clear</button>
      </template>
    </div>

    <div class="mb-3">
      <div class="section-title">Brands</div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="brand-amz" v-model="local.onlyAmazon" @change="apply">
        <label class="form-check-label" for="brand-amz">Amazon</label>
      </div>
    </div>

    <div class="mb-3">
      <div class="section-title">Price</div>
      <div class="d-flex align-items-center gap-2">
        <input type="number" class="form-control form-control-sm" placeholder="Min" v-model.number="local.min" @change="apply">
        <input type="number" class="form-control form-control-sm" placeholder="Max" v-model.number="local.max" @change="apply">
        <button class="btn btn-sm btn-primary" @click="apply">Go</button>
      </div>
      <small class="text-muted">EGP</small>
    </div>

    <div>
      <div class="section-title">Deals & Discounts</div>
      <button class="btn btn-sm btn-link p-0 d-block">All Discounts</button>
      <button class="btn btn-sm btn-link p-0 d-block">Today's Deals</button>
    </div>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'ShopFilters',
  props: { categories: { type: Array, default: () => [] }, loadingCats: { type: Boolean, default: false } },
  emits: ['change'],
  data(){
    return {
      local: {
        category_id: null,
        onlyAmazon: false,
        min: null,
        max: null,
      }
    }
  },
  methods: {
    apply(){ this.$emit('change', { ...this.local }) }
  }
}
</script>

<style scoped>
.filters { position: sticky; top: 84px; border: 1px solid #e9ecef; }
.section-title { font-weight: 600; color: #0d6efd; margin-bottom: .25rem; }
</style>
