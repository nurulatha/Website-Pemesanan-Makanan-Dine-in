<template>
  <div class="menu-card">
    <img :src="getImageUrl(product.image)" class="menu-image" />
    <div class="product-info">
      <h3 class="item-name">{{ product.name }}</h3>
      <span class="bg-main-gradient item-price"><span></span>Rp {{ product.price }}</span>
    </div>
  </div>
</template>

<script>

export default {
  name: "CardProduct",
  props: ["product"],
  methods: {
    getImageUrl(imageName) {
      return `${this.$apiURL}/storage/${imageName}`;
    },
  },
};
</script>

<style lang="css" scoped></style>
