import { createRouter, createWebHistory } from 'vue-router'
import { h } from 'vue'
import auth from '../store/auth'

// Pages
import LandingPage from '../components/pages/Landing.vue'
import FeaturesPage from '../components/pages/Features.vue'
import AboutPage from '../components/pages/About.vue'
import ContactPage from '../components/pages/Contact.vue'
import LoginPage from '../components/pages/Login.vue'
import RegisterPage from '../components/pages/Register.vue'
import DashboardPage from '../components/pages/Dashboard.vue'
import AdminDashboard from '../components/pages/admin/Dashboard.vue'
import ParentDashboard from '../components/pages/ParentDashboard.vue'
import AdminLayout from '../components/layouts/AdminLayout.vue'
import AdminUsers from '../components/pages/admin/Users.vue'
import AdminLogs from '../components/pages/admin/Logs.vue'
import AdminSettings from '../components/pages/admin/Settings.vue'
import SubjectPage from '../components/pages/Subject.vue'
import AdminSubjects from '../components/pages/admin/Subjects.vue'
import AdminBooks from '../components/pages/admin/Books.vue'
import AdminProducts from '../components/pages/admin/AdminProducts.vue'
import AdminProviders from '../components/pages/admin/AdminProviders.vue'
import AdminChats from '../components/pages/admin/AdminChats.vue'
import ProfilePage from '../components/pages/Profile.vue'
import AdminUserProfile from '../components/pages/admin/UserProfile.vue'
import SubjectsPage from '../components/pages/Subjects.vue'
import AdvisorPage from '../components/pages/Advisor.vue'
import ChatPage from '../components/pages/Chat.vue'
import MessagesPage from '../components/pages/Messages.vue'
import OnboardingPage from '../components/pages/Onboarding.vue'
import AdaptivePracticePage from '../components/pages/AdaptivePractice.vue'
// Vendor
import VendorLayout from '../components/layouts/VendorLayout.vue'
import VendorDashboard from '../components/pages/vendor/Dashboard.vue'
import VendorProducts from '../components/pages/vendor/Products.vue'
import VendorProductForm from '../components/pages/vendor/ProductForm.vue'
import VendorProductEdit from '../components/pages/vendor/ProductEdit.vue'
import VendorPosts from '../components/pages/vendor/Posts.vue'
import VendorCommunications from '../components/pages/vendor/Communications.vue'
import ChatConversation from '../components/pages/ChatConversation.vue'
import AdminProductEdit from '../components/pages/admin/AdminProductEdit.vue'
import VendorPostForm from '../components/pages/vendor/PostForm.vue'
// Public listings
import ProductsPublic from '../components/pages/Products.vue'
import PostsPublic from '../components/pages/Posts.vue'
import CartPage from '../components/pages/Cart.vue'
import ProductDetail from '../components/pages/ProductDetail.vue'

export const routes = [
  { path: '/', name: 'home', component: LandingPage, meta: { title: 'Home' } },
  { path: '/features', name: 'features', component: FeaturesPage, meta: { title: 'Features' } },
  { path: '/about', name: 'about', component: AboutPage, meta: { title: 'About' } },
  { path: '/contact', name: 'contact', component: ContactPage, meta: { title: 'Contact' } },
  { path: '/login', name: 'login', component: LoginPage, meta: { guestOnly: true, title: 'Login' } },
  { path: '/register', name: 'register', component: RegisterPage, meta: { guestOnly: true, title: 'Register' } },
  { path: '/dashboard', name: 'dashboard', component: DashboardPage, meta: { requiresAuth: true, title: 'Dashboard' } },
  { path: '/parent/dashboard', name: 'parent-dashboard', component: ParentDashboard, meta: { requiresAuth: true, title: 'Parent Dashboard' } },
  { path: '/profile', name: 'profile', component: ProfilePage, meta: { requiresAuth: true, title: 'Profile' } },
  { path: '/subjects', name: 'subjects', component: SubjectsPage, meta: { title: 'Subjects' } },
  { path: '/subjects/:subject', name: 'subject', component: SubjectPage, props: true, meta: { title: 'Subject' } },
  { path: '/advisor', name: 'advisor', component: AdvisorPage, meta: { title: 'Study Advisor' } },
  { path: '/chat', name: 'chat', component: ChatPage, meta: { title: 'AI Chat' } },
  { path: '/messages', name: 'messages', component: MessagesPage, meta: { requiresAuth: true, title: 'Messages' } },
  { path: '/onboarding', name: 'onboarding', component: OnboardingPage, meta: { title: 'Onboarding' } },
  { path: '/practice', name: 'practice', component: AdaptivePracticePage, meta: { title: 'Adaptive Practice' } },
  // Public listings
  { path: '/products', name: 'products', component: ProductsPublic, meta: { title: 'Products' } },
  { path: '/products/:id', name: 'product-detail', component: ProductDetail, props: true, meta: { title: 'Product Details' } },
  { path: '/posts', name: 'posts', component: PostsPublic, meta: { title: 'Posts' } },
  { path: '/cart', name: 'cart', component: CartPage, meta: { title: 'Cart' } },
  { path: '/chat/:id', name: 'chat-conversation', component: ChatConversation, props: true, meta: { title: 'Chat', requiresAuth: true } },
  // Admin area under shared layout
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true, admin: true },
    children: [
      { path: 'dashboard', name: 'admin-dashboard', component: AdminDashboard, meta: { title: 'Admin Dashboard' } },
      { path: 'users', name: 'admin-users', component: AdminUsers, meta: { title: 'Admin Users' } },
      { path: 'users/:id/profile', name: 'admin-user-profile', component: AdminUserProfile, props: true, meta: { title: 'Admin User Profile' } },
      { path: 'logs', name: 'admin-logs', component: AdminLogs, meta: { title: 'Admin Logs' } },
      { path: 'settings', name: 'admin-settings', component: AdminSettings, meta: { title: 'Admin Settings' } },
      { path: 'subjects', name: 'admin-subjects', component: AdminSubjects, meta: { title: 'Admin Subjects' } },
      { path: 'books', name: 'admin-books', component: AdminBooks, meta: { title: 'Admin Books' } },
      { path: 'products', name: 'admin-products', component: AdminProducts, meta: { title: 'Admin Products' } },
      { path: 'products/:id/edit', name: 'admin-product-edit', component: AdminProductEdit, props: true, meta: { title: 'Edit Product' } },
      { path: 'providers', name: 'admin-providers', component: AdminProviders, meta: { title: 'Admin Providers' } },
      { path: 'chats', name: 'admin-chats', component: AdminChats, meta: { title: 'Admin Chats' } },
    ]
  },
  // Vendor area under vendor layout
  {
    path: '/vendor',
    component: VendorLayout,
    meta: { requiresAuth: true, requiresProvider: true },
    children: [
      { path: 'dashboard', name: 'vendor-dashboard', component: VendorDashboard, meta: { title: 'Vendor Dashboard' } },
      { path: 'products', name: 'vendor-products', component: VendorProducts, meta: { title: 'My Products' } },
      { path: 'products/new', name: 'vendor-product-new', component: VendorProductForm, meta: { title: 'Add Product' } },
      { path: 'products/:id/edit', name: 'vendor-product-edit', component: VendorProductEdit, props: true, meta: { title: 'Edit Product' } },
      { path: 'posts', name: 'vendor-posts', component: VendorPosts, meta: { title: 'My Posts' } },
      { path: 'posts/new', name: 'vendor-post-new', component: VendorPostForm, meta: { title: 'Add Post' } },
      { path: 'communications', name: 'vendor-communications', component: VendorCommunications, meta: { title: 'Communications' } },
    ]
  },
  { path: '/:pathMatch(.*)*', redirect: { name: 'home' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() { return { top: 0, behavior: 'smooth' } }
})

// Load state from storage when router initializes
auth.loadFromLocalStorage()

router.beforeEach(async (to) => {
  if (to.meta?.requiresAuth && !auth.isAuthenticated()) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // If navigating to default dashboard but user is parent, redirect to parent dashboard
  if (to.name === 'dashboard' && auth.isAuthenticated()) {
    const role = auth.state.user?.role
    if (role === 'parent') return { name: 'parent-dashboard' }
    if (role === 'provider') return { name: 'vendor-dashboard' }
  }
  // Guests cannot access login/register; redirect based on role
  if (to.meta?.guestOnly && auth.isAuthenticated()) {
    const role = auth.state.user?.role
    if (role === 'parent') return { name: 'parent-dashboard' }
    if (role === 'provider') return { name: 'vendor-dashboard' }
    return { name: 'dashboard' }
  }

  // Admin role guard
  if (to.meta?.requiresAdmin) {
    // Ensure we have the latest user profile
    if (!auth.state.user && auth.isAuthenticated()) {
      await auth.fetchUser()
    }
    const role = auth.state.user?.role
    if (role !== 'admin') {
      return { name: 'dashboard', query: { noadmin: '1' } }
    }
  }

  // Provider role guard
  if (to.meta?.requiresProvider) {
    if (!auth.state.user && auth.isAuthenticated()) {
      await auth.fetchUser()
    }
    const role = auth.state.user?.role
    if (role !== 'provider') {
      return { name: 'dashboard', query: { noprovider: '1' } }
    }
  }
})

// Dynamic page titles
const DEFAULT_TITLE = 'Fix & Shop'
router.afterEach((to) => {
  const nearestWithTitle = [...to.matched].reverse().find(r => r.meta && r.meta.title)
  if (nearestWithTitle) {
    document.title = `${nearestWithTitle.meta.title} | ${DEFAULT_TITLE}`
  } else {
    document.title = DEFAULT_TITLE
  }
})

export default router
