<template>
  <div v-if="isVisible" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>{{ product.name }}</h3>
        <button class="close-button" @click="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <img :src="getImageUrl(product.image)" class="product-image" />
        <p class="product-description">{{ product.description }}</p>
        <span class="product-price"><span></span> Rp {{ product.price }}</span>

      </div>
      <div class="modal-footer">
        <div class="quantity-selector">
          <button @click="decreaseQuantity">-</button>
          <input type="number" v-model.number="quantity" min="1" />
          <button @click="increaseQuantity">+</button>
        </div>
        <button class="btn btn-main-gradient" @click="addToCart">Tambah ke Keranjang</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "DetailMenu",
  props: {
    product: Object,
    isVisible: Boolean,
  },
  data() {
    return {
      quantity: 1,
    };
  },
  methods: {
    closeModal() {
      this.$emit("close");
    },
    addToCart() {
      this.$emit("add-to-cart", { ...this.product, quantity: this.quantity });
    },
    getImageUrl(imageName) {
      return `${this.$apiURL}/storage/${imageName}`;
    },
    increaseQuantity() {
      this.quantity += 1;
    },
    decreaseQuantity() {
      if (this.quantity > 1) {
        this.quantity -= 1;
      }
    },
  },
};
</script>
<style scoped>
</style>
