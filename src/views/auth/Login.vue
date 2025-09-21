<template>
  <div class="container h-full px-4 mx-auto">
    <div class="flex items-center content-center justify-center h-full">
      <div class="w-full px-4 lg:w-4/12">
        <div class="relative flex flex-col w-full min-w-0 mb-6 break-words border-0 rounded-lg shadow-lg bg-blueGray-200">
          <div class="flex-auto px-4 py-10 pt-0 lg:px-10">
            <form>
              <div class="relative w-full mb-3">
                <label class="block mt-6 mb-2 text-xs font-bold uppercase text-blueGray-600" htmlFor="grid-password"> Username </label>
                <input
                  v-model="username"
                  type="text"
                  class="w-full px-3 py-3 text-sm transition-all duration-150 ease-linear bg-white border-0 rounded shadow placeholder-blueGray-300 text-blueGray-600 focus:outline-none focus:ring"
                  placeholder="Username"
                />
              </div>

              <div class="relative w-full mb-3">
                <label class="block mb-2 text-xs font-bold uppercase text-blueGray-600" htmlFor="grid-password"> Password </label>
                <input
                  v-model="password"
                  type="password"
                  class="w-full px-3 py-3 text-sm transition-all duration-150 ease-linear bg-white border-0 rounded shadow placeholder-blueGray-300 text-blueGray-600 focus:outline-none focus:ring"
                  placeholder="Password"
                />
              </div>

              <div class="mt-6 text-center">
                <button
                  @click="login"
                  class="w-full px-6 py-3 mb-1 mr-1 text-sm font-bold text-white uppercase transition-all duration-150 ease-linear rounded shadow outline-none bg-blueGray-800 active:bg-blueGray-600 hover:shadow-lg focus:outline-none"
                  type="button"
                >
                  Sign In
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  data() {
    return {
      username: "",
      password: "",
    };
  },
  methods: {
    login() {
      axios
        .post(`${this.$apiURL}/api/login`, {
          username: this.username,
          password: this.password,
        })
        .then((response) => {
          const token = response.data.token;
          localStorage.setItem("auth_token", token);
          this.$router.push("/admin/menus");
          this.$toast.success("Login Successful!");
        })
        .catch((error) => {
          console.error("Login failed:", error.response);
          this.$toast.error("Invalid credentials");
        });
    },
  },
  
};
</script>
