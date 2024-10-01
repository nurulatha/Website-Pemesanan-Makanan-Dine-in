<template>
  <div class="w-screen h-screen flex">
    <!-- Side bar -->
    <div class="w-[300px] h-full bg-gray-200 text-white" v-show="showSide">
      <div class="h-[50px] bg-gray-900 flex justify-start items-center">
        <div class="px-[20px]">
          <h3 class="font-bold text-xl">Admin Dashboard</h3>
        </div>
      </div>
      <div class="h-[calc(100vh-50px)] bg-gray-800 py-[20px]">
        <div class="flex flex-col justify-between h-full px-[20px] space-y-[10px]">
          <div class="flex flex-col justify-between space-y-[10px]">
            <router-link
              to="/admin/menu"
              class="inline-flex relative items-center py-[10px] px-[10px] w-full text-sm font-medium rounded-md border-gray-200 hover:bg-gray-300 hover:text-gray-800 transition duration-400 ease-in-out"
            >
              <i class="fa-solid fa-list text-[20px] mr-3"></i>
              Menu
            </router-link>
            <router-link
              to="/admin/table"
              class="inline-flex relative items-center py-[10px] px-[10px] w-full text-sm font-medium rounded-md border-gray-200 hover:bg-gray-300 hover:text-gray-800 transition duration-400 ease-in-out"
            >
              <i class="fa-solid fa-utensils text-[20px] mr-3"></i>
              Table
            </router-link>
            <div>
              <div
                @click="toggleOrderDropdown"
                class="inline-flex relative items-center py-[10px] px-[10px] w-full text-sm font-medium rounded-md border-gray-200 hover:bg-gray-200 hover:text-gray-800 transition duration-400 ease-in-out cursor-pointer"
              >
                <i class="fa-solid fa-sliders text-[20px] mr-3"></i>
                Orders
                <i :class="orderDropdown ? 'fa-chevron-up' : 'fa-chevron-down'" class="fa-solid ml-auto"></i>
              </div>
              <!-- Dropdown items -->
              <div v-show="orderDropdown" class="ml-8 mt-2 space-y-2">
                <router-link
                  to="/admin/orders/table1"
                  class="inline-flex items-center py-[5px] px-[10px] w-full text-sm font-medium rounded-md hover:bg-gray-200 hover:text-gray-800 transition duration-400 ease-in-out"
                >
                  Meja 1
                </router-link>
                <router-link
                  to="/admin/orders/table2"
                  class="inline-flex items-center py-[5px] px-[10px] w-full text-sm font-medium rounded-md hover:bg-gray-200 hover:text-gray-800 transition duration-400 ease-in-out"
                >
                  Meja 2
                </router-link>
                <router-link
                  to="/admin/orders/table3"
                  class="inline-flex items-center py-[5px] px-[10px] w-full text-sm font-medium rounded-md hover:bg-gray-200 hover:text-gray-800 transition duration-400 ease-in-out"
                >
                  Meja 3
                </router-link>
              </div>
            </div>
          </div>
          <div class="h-[50px]">
            <div></div>
          </div>
        </div>
      </div>
    </div>
    <div class="w-full h-full bg-gray-400">
      <div class="h-[50px] bg-gray-100 flex items-center shadow-sm px-[20px] w-full py-[10px] z-10 border-b">
        <!-- Hambuger menu -->
        <div class="cursor-pointer w-[30px]" @click="toggleSideBar">
          <i class="fa-solid fa-bars text-2xl"></i>
        </div>
        <!-- Search bar -->
        <div class="w-[calc(100%-30px)] flex">
          <div class="w-[calc(100%-200px)] flex justify-center"></div>
          <!-- User login -->
          <div class="w-[200px]">
            <div class="flex items-center justify-start space-x-4" @click="toggleDrop">
              <img
                class="w-10 h-10 rounded-full border-2 border-gray-50"
                src="https://yt3.ggpht.com/hqsxh-Vnbw9OK0_X4DAWh6RkmEUVnL-82SRCyh-IKr9fIXR8zhUCRdBEwgWWL_14q_L8Piod=s108-c-k-c0x00ffffff-no-rj"
                alt=""
              />
              <div class="font-semibold dark:text-white text-left">
                <div>Madona ,Dev OP</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Admin</div>
              </div>
            </div>
            <!-- Drop down -->
            <div
              v-show="showDropDown"
              class="absolute right-[10px] z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              role="menu"
              aria-orientation="vertical"
              aria-labelledby="menu-button"
              tabindex="-1"
            >
              <div class="py-1 text-left" role="none">
                <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
                <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-0">Account settings</a>
                <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-1">Support</a>
                <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-2">License</a>
                <button type="submit" class="text-gray-700 block w-full px-4 py-2 text-left text-sm" role="menuitem" tabindex="-1" id="menu-item-3" @click="logout">Sign out</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="h-[calc(100vh-50px)] bg-gray-50 p-[20px]">
        <div class="border border-gray-300 rounded-md p-[20px] h-full">
          <router-view></router-view>
        </div>
      </div>
    </div>
    <!-- Main  -->
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "AdminDashboard",
  data() {
    return {
      showDropDown: false,
      showSide: true,
      orderDropdown: false,
    };
  },
  methods: {
    logout() {
      axios
        .get(`${this.$apiURL}/api/logout`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
        })
        .then(() => {
          localStorage.removeItem("auth_token");
          this.$router.push("/");
          this.$toast.success("Logout Successful!");
        })
        .catch((error) => {
          console.error("Error logging out:", error.response || error);
          this.$toast.error("Logout Failed");
        });
    },
    toggleOrderDropdown() {
      this.orderDropdown = !this.orderDropdown;
    },

    // hide show side bar
    toggleSideBar() {
      this.showSide = !this.showSide;
    },
    // toggle user
    toggleDrop() {
      this.showDropDown = !this.showDropDown;
    },
  },
};
</script>

<style></style>
