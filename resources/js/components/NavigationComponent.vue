<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="#"></a>
        <h2>{{ $route.meta.title || 'Система учета для селлера' }}</h2>
        <div class="navbar-nav">
            <router-link to="/profile" class="nav-link">Профиль</router-link>
            <router-link to="/product" class="nav-link">Товары</router-link>
            <router-link to="/category" class="nav-link">Категории товаров</router-link>
            <router-link to="/order" class="nav-link">Мои заказы</router-link>
        
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      <router-view></router-view> 
    </div>
  </div>
</template>

<script>
export default {
  name: 'NavigationComponent',
  mounted() {
    console.log("Navigation mounted.");
  },
};
</script>