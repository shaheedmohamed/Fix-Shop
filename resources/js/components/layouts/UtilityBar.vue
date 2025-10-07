<template>
  <div class="utility-bar d-none d-lg-block position-relative">
    <div class="container py-2 d-flex align-items-center gap-3">
      <!-- Full-width search occupying the bar -->
      <form class="flex-grow-1 d-flex" @submit.prevent="submitSearch" role="search" aria-label="Search products">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input ref="searchInput" v-model.trim="term" type="search" class="form-control" placeholder="Search products, brands or keywords..." @focus="openPanel=true" @input="onType" />
          <button class="btn btn-primary">Search</button>
        </div>
      </form>

      <!-- Quick links -->
      <RouterLink to="/contact" class="link-underline small ms-2">
        <i class="fa-regular fa-envelope me-1"></i> Contact
      </RouterLink>
      <button class="btn btn-link p-0 link-underline small" @click="messageAdmin">
        <i class="fa-regular fa-message me-1"></i> Support Team
      </button>
    </div>

    <!-- Suggestions panel spanning full width of the bar -->
    <div v-if="openPanel" class="suggestions shadow" ref="panel">
      <div class="container py-2">
        <div v-if="loading" class="text-center py-2"><div class="spinner-border spinner-border-sm text-light"></div></div>
        <template v-else>
          <div v-if="recentTerms.length>0" class="mb-2">
            <div class="text-uppercase xsmall text-muted mb-1">Recent</div>
            <div class="chips">
              <button v-for="t in recentTerms" :key="'r-'+t" class="chip" @click="applySuggestion(t)">{{ t }}</button>
            </div>
          </div>
          <div v-if="suggestions.length>0">
            <div class="text-uppercase xsmall text-muted mb-1">Suggestions</div>
            <div class="row g-2">
              <div v-for="s in suggestions" :key="s.id" class="col-12 col-md-6 col-lg-4">
                <button class="sugg-item w-100 d-flex align-items-center gap-2" @click="goToProduct(s)">
                  <img :src="imageUrl(s.image_path)" alt="" class="thumb" />
                  <div class="text-start">
                    <div class="name text-truncate">{{ s.name }}</div>
                    <div class="muted xsmall">EGP {{ displayPrice(s).toLocaleString() }}</div>
                  </div>
                </button>
              </div>
            </div>
          </div>
          <div v-if="suggestions.length===0 && recentTerms.length===0" class="text-muted small">Start typing to search products...</div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import messages from '../../store/messages'
export default {
  name: 'UtilityBar',
  setup(){
    const router = useRouter()
    const term = ref('')
    const suggestions = ref([])
    const loading = ref(false)
    const openPanel = ref(false)
    const searchInput = ref(null)
    const panel = ref(null)

    const recentTerms = computed(() => {
      try{
        const raw = localStorage.getItem('search_hits')
        const obj = JSON.parse(raw || '{}')
        return Object.entries(obj).sort((a,b)=>b[1]-a[1]).map(x=>x[0]).slice(0,8)
      }catch{ return [] }
    })

    let tHandle = null
    const onType = () => {
      if (tHandle) clearTimeout(tHandle)
      tHandle = setTimeout(fetchSuggestions, 250)
    }

    const fetchSuggestions = async () => {
      const q = (term.value || '').trim()
      if (!q) { suggestions.value = []; return }
      loading.value = true
      try{
        const { data } = await axios.get('/api/products', { params: { q, limit: 9 } })
        suggestions.value = data?.data || data || []
      } finally { loading.value = false }
    }

    const recordSearch = (q) => {
      try{
        const raw = localStorage.getItem('search_hits')
        const obj = raw ? JSON.parse(raw) : {}
        obj[q] = (obj[q] || 0) + 1
        localStorage.setItem('search_hits', JSON.stringify(obj))
      }catch{}
    }

    const submitSearch = () => {
      const q = (term.value || '').trim()
      if (!q) return
      recordSearch(q)
      openPanel.value = false
      router.push({ name: 'products', query: { q } })
    }

    const goToProduct = (p) => {
      openPanel.value = false
      router.push({ name: 'product-detail', params: { id: p.id } })
    }

    const applySuggestion = (t) => {
      term.value = t
      submitSearch()
    }

    const onClickOutside = (e) => {
      const el = panel.value
      const input = searchInput.value
      if (openPanel.value && el && !el.contains(e.target) && input && !input.contains(e.target)) {
        openPanel.value = false
      }
    }

    onMounted(() => {
      document.addEventListener('click', onClickOutside, { passive: true })
    })
    onBeforeUnmount(() => {
      document.removeEventListener('click', onClickOutside)
    })

    const displayPrice = (p) => {
      const price = Number(p.price || 0)
      const discount = Number(p.price_discount || 0)
      return (discount && discount > 0 && discount < price) ? discount : price
    }
    const imageUrl = (p) => {
      if (!p) return '/images/placeholder-product.svg'
      if (typeof p !== 'string') return '/images/placeholder-product.svg'
      if (p.startsWith('http')) return p
      if (p.startsWith('/storage/')) return p
      if (p.startsWith('img/')) return `/storage/${p}`
      return `/storage/img/${p}`
    }

    const messageAdmin = async () => {
      try {
        const id = await messages.startConversationWithAdmin('Support request: Hello! I need help.')
        if (id) router.push({ name: 'messages', query: { c: id } })
      } catch (_) {
        router.push({ name: 'messages' })
      }
    }

    return { term, suggestions, loading, openPanel, recentTerms, onType, submitSearch, goToProduct, applySuggestion, messageAdmin, displayPrice, imageUrl, searchInput, panel }
  }
}
</script>

<style scoped>
.utility-bar { background: #0b2239; color: #cfe0f2; }
.link-underline { color: #cfe0f2; text-decoration: none; }
.link-underline:hover { text-decoration: underline; color: #ffffff; }

/* Suggestions panel overlays the bar */
.suggestions { position: absolute; left: 0; right: 0; top: 100%; background: #0f2133; color: #e6eef8; border-bottom: 1px solid rgba(255,255,255,.08); z-index: 50; }
.suggestions .name { font-weight: 600; }
.suggestions .muted { color: #cfe0f2; opacity: .8; }
.thumb { width: 36px; height: 36px; object-fit: cover; border-radius: 6px; background: #fff; }
.sugg-item { background: transparent; border: 1px solid rgba(255,255,255,.08); padding: 6px 8px; border-radius: 8px; color: inherit; text-align: left; }
.sugg-item:hover { background: rgba(255,255,255,.06); }
.xsmall { font-size: .75rem; }
.chips { display: flex; flex-wrap: wrap; gap: 6px; }
.chip { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); color: #fff; border-radius: 999px; padding: 4px 10px; }
.chip:hover { background: rgba(255,255,255,.15); }
</style>
