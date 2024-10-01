<template>
  <div>
    <div class="front-header navbar-border">
      <nav>
        <div>Foodie <i class="fa-solid fa-bowl-food"></i></div>
        <div class="front-nav-links">
          <button class="btn btn-main-gradient" @click="toggleCart">
            <i class="fa-solid fa-cart-shopping"></i>&nbsp;
            <span>{{ cartLength }}</span>
          </button>
        </div>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: "NavBar",
  props: {
    cartLength: {
      type: Number,
      required: true,
    },
  },
  methods: {
    toggleCart() {
      this.$emit("toggle-cart");
    },
  },
};
</script>
