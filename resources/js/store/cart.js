import { reactive } from 'vue'

const state = reactive({
  items: [], // {id, name, price, qty, image}
  toastMsg: ''
})

function save(){
  try{ localStorage.setItem('sp_cart', JSON.stringify(state.items)) }catch(e){}
}

function load(){
  try{
    const raw = localStorage.getItem('sp_cart')
    if (raw) state.items = JSON.parse(raw)
  }catch(e){ state.items = [] }
}

function add(item){
  const i = state.items.findIndex(x => x.id === item.id)
  if (i >= 0) {
    state.items[i].qty = (state.items[i].qty || 1) + 1
  } else {
    state.items.unshift({ id: item.id, name: item.name, price: Number(item.price||0), qty: 1, image: item.image || '' })
  }
  save()
}

function remove(id){
  state.items = state.items.filter(x => x.id !== id)
  save()
}

function clear(){ state.items = []; save() }

function count(){ return state.items.reduce((a,b)=>a+(b.qty||1),0) }
function total(){ return state.items.reduce((a,b)=>a+(b.price*(b.qty||1)),0) }

let toastTimer = null
function toast(msg){
  state.toastMsg = msg
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(()=>{ state.toastMsg = '' }, 1500)
}

load()

export default { state, add, remove, clear, count, total, toast }
