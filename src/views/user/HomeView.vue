<template>
  <div class="front-wrapper">
    <NavBar :cartLength="cart.length" :tableId="tableId" @toggle-cart="toggleCart" />
    <div class="front-main">
      <div class="main-grid">
        <div class="menu-section">
          <div class="image-hero">
            <img src="@/assets/img1.jpg" class="responsive-img" />
            <div class="text-overlay">
              <div class="main-text">The Sushi Migrant</div>
              <div class="text-under">SCBD (Sudirman Central Business District), Jakarta Selatan</div>
            </div>
          </div>

          <div class="category-nav-wrapper">
            <button class="nav-arrow left-arrow" @click="scrollNav('left')">‹</button>
            <div class="category-nav" ref="categoryNav">
              <button v-for="category in categories" :key="category" :class="{ 'active-category': selectedCategory === category }" @click="scrollToCategory(category)">
                {{ category }}
              </button>
            </div>
            <button class="nav-arrow right-arrow" @click="scrollNav('right')">›</button>
          </div>

          <div class="menu-grid">
            <CardProduct v-for="product in filteredProducts" :key="product.id" :product="product" @click="showProductDetail(product)" />
          </div>
        </div>

        <div :class="['cart-section', { 'is-visible': isCartVisible }]">
          <div class="cart-card">
            <button class="close-cart-btn" @click="toggleCart">
              <i class="fa-solid fa-x"></i>
            </button>
            <div class="cart-header">
              <h3>New Order</h3>
              <small>{{ cart.length }} items in cart</small>
            </div>

            <div class="cart-body">
              <div class="cart-items">
                <div v-for="item in cart" :key="item.id" class="cart-item">
                  <div class="cart-info">
                    <span class="fa-solid fa-trash" @click="removeFromCart(item)"></span>
                    <div>
                      <h5>{{ item.name }}</h5>
                      <small>Rp {{ item.price }} x {{ item.quantity }}</small>
                    </div>
                  </div>
                  <div class="cart-controls">
                    <input type="text" readonly :value="item.quantity" />
                    <div>
                      <span class="fa-solid fa-angle-up" @click="increaseQuantity(item)"></span>
                      <span class="fa-solid fa-angle-down" @click="decreaseQuantity(item)"></span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="cart-sum">
                <div class="cart-address"></div>
                <div class="price-flex">
                  <small>Total</small>
                  <h4>Rp {{ cartTotal }}</h4>
                </div>

                <div class="cart-pay-btn">
                  <input type="text" v-model="customerName" placeholder="Enter your name" class="form-control mb-2" />
                  <button class="btn btn-success" v-if="orderId !== null" @click="payOrder(orderId)"><span class="fa-regular fa-credit-card"></span> Bayar</button>
                  <button class="btn btn-success" v-else @click="submitOrder"><span class="fa-regular fa-credit-card"></span> Pesan</button>
                  <br />
                  <button class="btn btn-success">*Pembayaran dilakukan diakhir</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <DetailMenu v-if="selectedProduct" :product="selectedProduct" :isVisible="isModalVisible" @close="closeModal" @add-to-cart="addToCart" />
  </div>
</template>

<script>
import NavBar from "@/components/Nav.vue";
import CardProduct from "@/components/CardProduct.vue";
import DetailMenu from "@/components/DetailMenu.vue";
import axios from "axios";

export default {
  name: "HomeView",
  components: { NavBar, CardProduct, DetailMenu },
  data() {
    return {
      products: [],
      categories: ["All"],
      selectedCategory: "All",
      selectedProduct: null,
      isModalVisible: false,
      cart: [],
      isCartVisible: false,
      customerName: "",
      tableId: null,
      orderId: null,
    };
  },
  methods: {
    setProduct(data) {
      this.products = data;
      const categories = [...new Set(data.map((product) => product.category))];
      this.categories = ["All", ...categories];
    },
    scrollToCategory(category) {
      this.selectedCategory = category;
    },
    scrollNav(direction) {
      const nav = this.$refs.categoryNav;
      const scrollAmount = 200;

      if (direction === "left") {
        nav.scrollBy({ left: -scrollAmount, behavior: "smooth" });
      } else if (direction === "right") {
        nav.scrollBy({ left: scrollAmount, behavior: "smooth" });
      }
    },
    showProductDetail(product) {
      this.selectedProduct = product;
      this.isModalVisible = true;
    },
    closeModal() {
      this.isModalVisible = false;
      this.selectedProduct = null;
    },
    addToCart(product) {
      const existingItem = this.cart.find((item) => item.id === product.id);

      if (existingItem) {
        existingItem.quantity += product.quantity;
      } else {
        const newItem = { ...product, quantity: product.quantity };
        this.cart.push(newItem);
      }

      localStorage.setItem(`cart_${this.tableId}`, JSON.stringify(this.cart));
      this.closeModal();
      this.$toast.success("Add to cart");
    },

    increaseQuantity(item) {
      item.quantity += 1;
      localStorage.setItem(`cart_${this.tableId}`, JSON.stringify(this.cart));
    },

    decreaseQuantity(item) {
      if (item.quantity > 1) {
        item.quantity -= 1;
        localStorage.setItem(`cart_${this.tableId}`, JSON.stringify(this.cart));
      }
    },
    removeFromCart(item) {
      this.cart = this.cart.filter((cartItem) => cartItem.id !== item.id);
      localStorage.setItem(`cart_${this.tableId}`, JSON.stringify(this.cart));
      this.$toast.success("Item removed");
    },
    submitOrder() {
      if (!this.customerName.trim()) {
        this.$toast.error("Please enter name");
        return;
      }
      const confirmation = confirm("Apakah pesanan Anda sudah sesuai?");
      if (!confirmation) {
        this.$toast.info("Pesanan dibatalkan");
        return;
      }
      const orderData = {
        url: this.tableId,
        customer_name: this.customerName,
        menu_items: this.cart.map((item) => ({
          menu_id: item.id,
          quantity: item.quantity,
        })),
      };
      axios
        .post(`${this.$apiURL}/api/orders`, orderData, { headers: { "ngrok-skip-browser-warning": "69420" } })
        .then((response) => {
          const orderItems = response.data.data.orderItems;
          if (orderItems && orderItems.length > 0) {
            this.orderId = orderItems[0].order_id;
            localStorage.setItem(`orderId_${this.tableId}`, this.orderId);
          }
          this.cart = [];
          localStorage.removeItem(`cart_${this.tableId}`);
          this.$toast.success("Order success", { autoClose: 1000 });
        })
        .catch((error) => console.error("Error submitting order:", error));
    },
    payOrder(orderId) {
      console.log("Order ID:", orderId);
      axios
        .post(`${this.$apiURL}/api/transactions`, {
          order_id: orderId,
        })
        .then((response) => {
          const checkoutUrl = response.data.data;
          window.location.href = checkoutUrl;
        })
        .catch((error) => {
          console.error("Error processing payment:", error.response ? error.response.data : error.message);
          this.$toast.error("Payment failed");
        });
    },
    checkPaymentStatus() {
      if (this.orderId) {
        axios
          .get(`${this.$apiURL}/api/transactions/status/${this.orderId}`, {
            headers: {
              "ngrok-skip-browser-warning": "69420",
            },
          })
          .then((response) => {
            const paymentStatus = response.data.status;
            if (paymentStatus === "PAID") {
              localStorage.removeItem(`orderId_${this.tableId}`);
              this.orderId = null;
              this.customerName = "";
              this.$toast.success("Payment successful");
            } else if (paymentStatus === "PENDING") {
              this.$toast.info("Payment is still pending");
            }
          })
          .catch((error) => {
            console.error("Error checking payment status:", error);
          });
      }
    },
    toggleCart() {
      if (window.innerWidth < 1200) {
        this.isCartVisible = !this.isCartVisible;
      }
    },
    extractTableId() {
      const match = window.location.href.match(/orders\/([\w\d]+)\/?/);
      if (match) {
        this.tableId = match[1];
      }
    },
  },
  computed: {
    cartTotal() {
      return this.cart.reduce((total, item) => total + item.price * item.quantity, 0);
    },
    filteredProducts() {
      if (this.selectedCategory === "All") {
        return this.products;
      }
      return this.products.filter((product) => product.category === this.selectedCategory);
    },
  },
  mounted() {
    this.extractTableId();
    this.cart = JSON.parse(localStorage.getItem(`cart_${this.tableId}`)) || [];
    const storedOrderId = localStorage.getItem(`orderId_${this.tableId}`);
    if (storedOrderId) {
      this.orderId = storedOrderId;
    }
    this.checkPaymentStatus();

    axios
      .get(`${this.$apiURL}/api/menus`, {
        headers: {
          "ngrok-skip-browser-warning": "69420",
        },
      })
      .then((response) => {
        this.setProduct(response.data);
      })
      .catch((error) => {
        console.error("Error fetching products:", error);
      });
  },
};
</script>
