<template>
  <!-- Content only; AdminLayout provides sidebar/header -->
  <section>
    <!-- KPI Cards: Users, Visits, Daily Logins -->
    <div class="cards kpi">
      <div class="stat-card b-blue">
        <div class="stat-value">{{ kpis.users.toLocaleString() }}</div>
        <div class="stat-label">Total Users</div>
        <i class="fa-solid fa-users icon"></i>
      </div>
      <div class="stat-card b-purple">
        <div class="stat-value">{{ kpis.visits.toLocaleString() }}</div>
        <div class="stat-label">Total Visits</div>
        <i class="fa-regular fa-eye icon"></i>
      </div>
      <div class="stat-card b-green">
        <div class="stat-value">{{ kpis.dailyLogins.toLocaleString() }}</div>
        <div class="stat-label">Daily Logins</div>
        <i class="fa-solid fa-right-to-bracket icon"></i>
      </div>
    </div>

    <!-- Charts: 2x2 grid -->
    <div class="charts-grid">
      <div class="chart-card">
        <div class="chart-title">Users Growth (Last 12 months)</div>
        <canvas ref="usersGrowthRef"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-title">Visits Trend (Last 30 days)</div>
        <canvas ref="visitsTrendRef"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-title">Daily Logins (This week)</div>
        <canvas ref="loginsDailyRef"></canvas>
      </div>
      <div class="chart-card">
        <div class="chart-title">Traffic Sources</div>
        <canvas ref="sourcesRef"></canvas>
      </div>
    </div>
  </section>
</template>

<script>
import { ref, onMounted, reactive } from 'vue'
import Chart from 'chart.js/auto'
import axios from 'axios'

export default {
  name: 'AdminDashboardPage',
  setup(){
    // Real KPIs (fetched)
    const kpis = reactive({ users: 0, visits: 0, dailyLogins: 0 })

    // Chart refs
    const usersGrowthRef = ref(null)
    const visitsTrendRef = ref(null)
    const loginsDailyRef = ref(null)
    const sourcesRef = ref(null)

    onMounted(async () => {
      // Fetch KPIs
      try {
        const { data } = await axios.get('/api/admin/dashboard/kpis')
        kpis.users = data.users || 0
        kpis.visits = data.visits || 0
        kpis.dailyLogins = data.dailyLogins || 0
      } catch (_) {}

      // Users Growth (Line)
      const usersGrowthData = await axios.get('/api/admin/dashboard/charts/users-growth').then(r=>r.data).catch(()=>({labels:[], data:[]}))
      new Chart(usersGrowthRef.value.getContext('2d'), {
        type: 'line',
        data: {
          labels: usersGrowthData.labels,
          datasets: [{
            label: 'Users',
            data: usersGrowthData.data,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,.15)',
            fill: true,
            tension: .35,
          }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      })

      // Visits Trend (Bar)
      const visitsTrendData = await axios.get('/api/admin/dashboard/charts/visits-trend').then(r=>r.data).catch(()=>({labels:[], data:[]}))
      new Chart(visitsTrendRef.value.getContext('2d'), {
        type: 'bar',
        data: {
          labels: visitsTrendData.labels,
          datasets: [{
            label: 'Visits',
            data: visitsTrendData.data,
            backgroundColor: '#8b5cf6'
          }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      })

      // Daily Logins (This week - Bar)
      const dailyLoginsData = await axios.get('/api/admin/dashboard/charts/daily-logins').then(r=>r.data).catch(()=>({labels:[], data:[]}))
      new Chart(loginsDailyRef.value.getContext('2d'), {
        type: 'bar',
        data: {
          labels: dailyLoginsData.labels,
          datasets: [{
            label: 'Logins',
            data: dailyLoginsData.data,
            backgroundColor: '#10b981'
          }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      })

      // Traffic Sources (Doughnut)
      const sources = await axios.get('/api/admin/dashboard/charts/sources').then(r=>r.data).catch(()=>({labels:[], data:[]}))
      new Chart(sourcesRef.value.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: sources.labels,
          datasets: [{
            data: sources.data,
            backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444']
          }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
      })
    })

    return { kpis, usersGrowthRef, visitsTrendRef, loginsDailyRef, sourcesRef }
  }
}
</script>

<style scoped>
.cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.cards.kpi { grid-template-columns: repeat(3, 1fr); margin-bottom: 14px; }
@media (max-width: 1199.98px){ .cards { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767.98px){ .cards { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 479.98px){ .cards { grid-template-columns: 1fr; } }

.stat-card { position: relative; background: #fff; border: 2px solid #e5e7eb; border-radius: 14px; padding: 16px; min-height: 92px; }
.stat-card .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; }
.stat-card .stat-label { color: #6b7280; font-weight: 500; }
.stat-card .icon { position: absolute; right: 12px; bottom: 10px; font-size: 20px; opacity: .8; }

.b-purple { border-color: #c4b5fd; box-shadow: inset 0 0 0 2px rgba(196,181,253,.25); }
.b-blue   { border-color: #93c5fd; box-shadow: inset 0 0 0 2px rgba(147,197,253,.25); }
.b-red    { border-color: #fca5a5; box-shadow: inset 0 0 0 2px rgba(252,165,165,.25); }
.b-green  { border-color: #a7f3d0; box-shadow: inset 0 0 0 2px rgba(167,243,208,.25); }
.b-gray   { border-color: #e5e7eb; box-shadow: inset 0 0 0 2px rgba(229,231,235,.35); }

/* Charts */
.charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
@media (max-width: 991.98px){ .charts-grid { grid-template-columns: 1fr; } }
.chart-card { background: #fff; border: 1px solid rgba(2,6,23,.06); border-radius: 12px; padding: 12px; }
.chart-title { font-weight: 600; color: #0f172a; margin-bottom: 6px; }
</style>
