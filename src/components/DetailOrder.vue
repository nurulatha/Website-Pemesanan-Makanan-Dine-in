<template>
  <div class="relative flex flex-col w-full min-w-0 mt-3 mb-6 break-words">
    <div class="block w-full overflow-x-auto">
      <h2 class="mb-4 text-2xl font-bold">Order for Table {{ tableId }}</h2>
      <form class="flex-row flex-wrap items-center md:flex lg:ml-auto">
        <div class="relative flex flex-wrap items-stretch w-full">
          <span class="absolute z-10 items-center justify-center w-8 h-full py-3 pl-3 text-base font-normal leading-snug text-center bg-transparent rounded text-blueGray-300">
            <i class="fas fa-search"></i>
          </span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search customer name..."
            class="relative w-full px-3 py-3 pl-10 text-sm bg-white border-0 rounded shadow outline-none placeholder-blueGray-300 text-blueGray-600 focus:outline-none focus:ring"
          />
        </div>
      </form>
      <table class="items-center w-full mt-6 bg-white border-collapse">
        <thead>
          <tr class="bg-gray-200 border-b border-gray-200">
            <th class="p-2 border border-slate-300">No</th>
            <th class="p-2 border border-slate-300">Customer Name</th>
            <th class="p-2 border border-slate-300">Customer Phone Number</th>
            <th class="p-2 border border-slate-300">Menu Item</th>
            <th class="p-2 border border-slate-300">Price</th>
            <th class="p-2 border border-slate-300">Quantity</th>
            <th class="p-2 border border-slate-300">Total Price</th>
            <th class="p-2 border border-slate-300">Status</th>
            <th class="p-2 border border-slate-300">Payment Method</th>
            <th class="p-2 border border-slate-300">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(order, index) in filteredOrders" :key="order.id" class="border-b border-gray-200">
            <td class="p-2 border border-slate-300">{{ index + 1 }}</td>
            <td class="p-2 border border-slate-300">{{ order.customer_name }}</td>
            <td class="p-2 border border-slate-300">{{ order.customer_phone }}</td>
            <td class="p-2 border border-slate-300">
              <ul>
                <li v-for="item in order.orderItems" :key="item.id">
                  {{ item.menu.name }}
                </li>
              </ul>
            </td>
            <td class="p-2 border border-slate-300">
              <ul>
                <li v-for="item in order.orderItems" :key="item.id">{{ item.menu.price }}</li>
              </ul>
            </td>
            <td class="p-2 border border-slate-300">
              <ul>
                <li v-for="item in order.orderItems" :key="item.id">
                  {{ item.quantity }}
                </li>
              </ul>
            </td>
            <td class="p-2 border border-slate-300">{{ calculateTotalOrderPrice(order.orderItems) | currency }}</td>
            <td class="p-2 border border-slate-300">
              {{ order.status || "Loading..." }}
              <button v-if="order.status === 'PENDING'" @click="confirmPayment(order.id)" class="ml-2 text-green-500 hover:text-green-700" title="Confirm Payment">
                <i class="fa fa-check"></i>
              </button>
            </td>
            <td class="p-2 border border-slate-300">
              {{ order.payment_method || "Loading..." }}
            </td>
            <td class="p-2 text-center border border-slate-300">
              <button @click="removeItem(order)" class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr v-if="orders.length === 0">
            <td colspan="8" class="p-2 text-center border border-slate-300">No orders found for this table.</td>
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
            this.orders[orderIndex].payment_method = response.data.payment_method;
          }
        })
        .catch((error) => {
          console.error(`Error fetching transaction status for order ${orderId}:`, error);
        });
    },
    confirmPayment(orderId) {
      axios
        .put(`${this.$apiURL}/api/transactions/offline/confirm-payment/${orderId}`, null, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "ngrok-skip-browser-warning": "69420",
          },
        })
        .then(() => {
          // Jika berhasil, ubah status order menjadi "PAID"
          let orderIndex = this.orders.findIndex((order) => order.id === orderId);
          if (orderIndex !== -1) {
            this.orders[orderIndex].status = "PAID";
          }
          this.$toast.success("Payment confirmed. Status updated to PAID.");
        })
        .catch((error) => {
          console.error("Error confirming payment:", error);
          this.$toast.error("Failed to confirm payment.");
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
