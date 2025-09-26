<template>
  <div class="secondary-nav">
    <div class="container d-flex align-items-center gap-3 overflow-auto no-scrollbar py-2">
      <button class="btn btn-link text-decoration-none text-light d-flex align-items-center" @click="toggleAll">
        <i class="fa-solid fa-bars me-2"></i> All
      </button>
      <div v-for="c in categories" :key="c.id" class="sec-item" @mouseenter="open=c.id" @mouseleave="open=null">
        <a href="#" class="sec-link" @click.prevent="goCategory(c)">{{ c.name }}</a>
        <div v-if="c.subcategories?.length && open===c.id" class="dropdown-menu shadow">
          <a v-for="s in c.subcategories" :key="s.id" href="#" class="dropdown-item" @click.prevent="goSub(c,s)">{{ s.name }}</a>
        </div>
      </div>
    </div>

    <!-- Slideout categories panel -->
    <transition name="fade">
      <aside v-if="showAll" class="all-panel">
        <div class="panel-header d-flex align-items-center justify-content-between">
          <strong>Categories</strong>
          <button class="btn btn-sm btn-light" @click="toggleAll"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="list-group list-group-flush small">
          <a v-for="c in categories" :key="c.id" href="#" class="list-group-item list-group-item-action" @click.prevent="goCategory(c)">
            {{ c.name }}
          </a>
        </div>
      </aside>
    </transition>
  </div>
   </template>

 <script>
 import axios from 'axios'
 export default {
   name: 'SecondaryNav',
   data(){
     return {
       showAll: false,
       categories: [],
       open: null,
     }
   },
   methods: {
     toggleAll(){ this.showAll = !this.showAll },
     async fetchCats(){
       try{
         const { data } = await axios.get('/api/catalog/categories')
         this.categories = data || []
       } catch(_){}
     },
     recordCatHit(id){
       try{
         const raw = localStorage.getItem('cat_hits')
         const obj = raw ? JSON.parse(raw) : {}
         obj[id] = (obj[id] || 0) + 1
         localStorage.setItem('cat_hits', JSON.stringify(obj))
       } catch(_){}
     },
     goCategory(c){
       this.recordCatHit(c.id)
       this.$router.push({ name: 'products', query: { category: c.id } })
     },
     goSub(c,s){
       this.recordCatHit(c.id)
       this.$router.push({ name: 'products', query: { category: c.id, subcategory: s.id } })
     }
   },
   mounted(){ this.fetchCats() }
 }
 </script>

 <style scoped>
.secondary-nav { background: #0f2133; color: #cfe0f2; position: relative; }
.sec-item { position: relative; }
.sec-link { color: #cfe0f2; text-decoration: none; white-space: nowrap; padding: 4px 6px; display: inline-block; }
.sec-link:hover { color: #fff; text-decoration: underline; }
.dropdown-menu { position: absolute; top: 100%; left: 0; background: #fff; border-radius: 8px; padding: 6px; min-width: 220px; }
.dropdown-item { display: block; padding: 6px 10px; color: #1f2937; text-decoration: none; border-radius: 6px; }
.dropdown-item:hover { background: #f3f4f6; }
.no-scrollbar { scrollbar-width: none; }
.no-scrollbar::-webkit-scrollbar { display: none; }

.all-panel {
  position: fixed;
  top: 0; left: 0; bottom: 0;
  width: 320px;
  background: #fff;
  box-shadow: 0 0 20px rgba(0,0,0,.2);
  z-index: 1055;
  padding: 12px;
  overflow: auto;
}
.panel-header { border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px; }

.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
