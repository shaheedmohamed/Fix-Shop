<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm nav-glass" v-reveal>
    <div class="container">
      <a class="navbar-brand d-flex align-items-center fw-bold" href="#top">
        <img src="/images/logo-smartpath.png" alt="Smart Path Logo" class="me-2 brand-logo" />
        <span>Smart Path</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item"><a class="nav-link" href="#top"><i class="fa-solid fa-house me-1 d-lg-none"></i>الرئيسية</a></li>
          <li class="nav-item"><a class="nav-link" href="#features"><i class="fa-solid fa-table-cells-large me-1 d-lg-none"></i>المميزات</a></li>
          <li class="nav-item"><a class="nav-link" href="#how"><i class="fa-solid fa-lightbulb me-1 d-lg-none"></i>كيف يعمل</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-primary ms-lg-3" href="#contact"><i class="fa-solid fa-user-plus me-1"></i>ابدأ الآن</a></li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default { name: 'NavBar' }
</script>
