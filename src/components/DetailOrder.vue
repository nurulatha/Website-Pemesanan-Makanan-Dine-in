<template>
  <div class="relative flex flex-col min-w-0 break-words w-full mb-6 mt-3">
    <div class="block w-full overflow-x-auto">
      <h2 class="text-2xl font-bold mb-4">Order for Table {{ tableId }}</h2>
      <form class="md:flex flex-row flex-wrap items-center lg:ml-auto">
        <div class="relative flex w-full flex-wrap items-stretch">
          <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
            <i class="fas fa-search"></i>
          </span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search customer name..."
            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
          />
        </div>
      </form>
      <table class="items-center w-full bg-white border-collapse mt-6">
        <thead>
          <tr class="bg-gray-200 border-b border-gray-200">
            <th class="border border-slate-300 p-2">No</th>
            <th class="border border-slate-300 p-2">Customer Name</th>
            <th class="border border-slate-300 p-2">Menu Item</th>
            <th class="border border-slate-300 p-2">Price</th>
            <th class="border border-slate-300 p-2">Quantity</th>
            <th class="border border-slate-300 p-2">Total Price</th>
            <th class="border border-slate-300 p-2">Status</th>
            <th class="border border-slate-300 p-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(order, index) in filteredOrders" :key="order.id" class="border-b border-gray-200">
            <td class="border border-slate-300 p-2">{{ index + 1 }}</td>
            <td class="border border-slate-300 p-2">{{ order.customer_name }}</td>
            <td class="border border-slate-300 p-2">
              <ul>
                <li v-for="item in order.orderItems" :key="item.id">
                  {{ item.menu.name }}
                </li>
              </ul>
            </td>
            <td class="border border-slate-300 p-2">
              <ul>
                <li v-for="item in order.orderItems" :key="item.id">Rp {{ item.menu.price }}</li>
              </ul>
            </td>
            <td class="border border-slate-300 p-2">
              <ul>
                <li v-for="item in order.orderItems" :key="item.id">
                  {{ item.quantity }}
                </li>
              </ul>
            </td>
            <td class="border border-slate-300 p-2">Rp {{ calculateTotalOrderPrice(order.orderItems) | currency }}</td>
            <td class="border border-slate-300 p-2">
              {{ order.status || "Loading..." }}
            </td>
            <td class="border border-slate-300 p-2 text-center">
              <button @click="removeItem(order)" class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr v-if="orders.length === 0">
            <td colspan="8" class="border border-slate-300 p-2 text-center">No orders found for this table.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      tableId: null,
      orders: [],
      searchQuery: "",
    };
  },
  created() {
    this.tableId = this.$route.params.tableId;
    this.getOrdersByTable();
  },
  watch: {
    "$route.params.tableId"(newTableId) {
      this.tableId = newTableId;
      this.getOrdersByTable();
    },
  },
  methods: {
    getOrdersByTable() {
      axios
        .get(`${this.$apiURL}/api/orders`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "ngrok-skip-browser-warning": "69420",
          },
        })
        .then((response) => {
          this.orders = response.data.data.filter((order) => order.table_id == this.tableId);
          this.orders.forEach((order) => {
            this.getTransactionStatus(order.id);
          });
        })
        .catch((error) => {
          console.error("Error fetching orders:", error);
          this.$toast.error("Failed to fetch orders");
        });
    },
    getTransactionStatus(orderId) {
      axios
        .get(`${this.$apiURL}/api/transactions/status/${orderId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "ngrok-skip-browser-warning": "69420",
          },
        })
        .then((response) => {
          let orderIndex = this.orders.findIndex((order) => order.id === orderId);
          if (orderIndex !== -1) {
            this.orders[orderIndex].status = response.data.status;
          }
        })
        .catch((error) => {
          console.error(`Error fetching transaction status for order ${orderId}:`, error);
        });
    },
    removeItem(order) {
      if (confirm(`Are you sure you want to delete the order for ${order.customer_name}?`)) {
        axios
          .delete(`${this.$apiURL}/api/orders/${order.id}`, {
            headers: {
              "ngrok-skip-browser-warning": "69420",
            },
          })
          .then(() => {
            this.getOrdersByTable();
            this.$toast.success("Order deleted successfully");
          })
          .catch((error) => {
            console.error("Error deleting item:", error);
            this.$toast.error("Failed to delete order");
          });
      }
    },
    calculateTotalOrderPrice(orderItems) {
      return orderItems.reduce((total, item) => total + item.total_price, 0);
    },
  },
  computed: {
    filteredOrders() {
      return this.orders.filter((order) => {
        const customerName = order.customer_name.toLowerCase();
        return customerName.startsWith(this.searchQuery.toLowerCase());
      });
    },
  },
};
</script>
