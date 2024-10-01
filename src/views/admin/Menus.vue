<template>
  <div class="relative flex flex-col min-w-0 break-words w-full mb-6 mt-3">
    <div class="block w-full overflow-x-auto">
      <div class="flex">
        <button @click="isCreatingMenu = true" class="Menu p-2 mb-9 bg-blueGray-200 shadow hover:shadow-lg"><i class="fa-solid fa-plus mr-2"></i> Create Menu</button>
        <button @click="isCreatingCategory = true" class="Menu p-2 mb-9 ml-5 bg-blueGray-200 shadow hover:shadow-lg"><i class="fa-solid fa-plus mr-2"></i>Create Category</button>
        <form class="md:flex flex-row flex-wrap items-center lg:ml-auto">
          <div class="relative flex w-full flex-wrap items-stretch">
            <span class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
              <i class="fas fa-search"></i>
            </span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search menu by name..."
              class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10"
            />
          </div>
        </form>
      </div>
      <!-- Projects table -->
      <table class="items-center w-full bg-transparent border-collapse mt-6">
        <thead>
          <tr>
            <th class="border border-slate-300 p-2">No</th>
            <th class="border border-slate-300 p-2">Name</th>
            <th class="border border-slate-300 p-2">Description</th>
            <th class="border border-slate-300 p-2">Price</th>
            <th class="border border-slate-300 p-2">Actions</th>
          </tr>
        </thead>
        <tbody v-for="(items, category) in groupedMenus" :key="category">
          <tr>
            <td colspan="6" class="border border-slate-300 p-2 bg-gray-200 font-bold">
              {{ category }}
              <span>
                <button @click="editCategory(category)" class="text-blue-500 hover:text-blue-700 ml-5 mr-3">
                  <i class="fa-solid fa-pen-to-square ml-3"></i>
                </button>
                <button @click="removeCategory(category)" class="text-red-500 hover:text-red-700">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </span>
            </td>
          </tr>
          <tr v-for="(item, index) in items" :key="item.id">
            <td class="border border-slate-300 p-2">{{ index + 1 }}</td>
            <td class="border border-slate-300 p-2">{{ item.name }}</td>
            <td class="border border-slate-300 p-2">{{ item.description }}</td>
            <td class="border border-slate-300 p-2">Rp{{ item.price }}</td>
            <td class="border border-slate-300 p-2 text-center">
              <button @click="editItem(item)" class="text-blue-500 hover:text-blue-700">
                <i class="fa-solid fa-pen-to-square mr-3"></i>
              </button>
              <button @click="removeItem(item)" class="text-red-500 hover:text-red-700 ml-4">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Popup Creating Menu -->
    <div v-if="isCreatingMenu" class="mt-12 overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="pr-4 relative max-w-xs mx-auto max-w-sm">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between px-6 py-2 border-b border-solid border-blueGray-200 rounded-t">
            <h3 class="text-xl font-semibold">Create Menu</h3>
          </div>
          <!--body-->
          <div class="relative p-2 flex-auto">
            <p class="my-4 mx-4 text-blueGray-500 text-sm">
              <label for="name">Name:</label>
              <input v-model="createForm.name" type="text" id="name" class="w-full p-1 border border-gray-300 rounded mb-2" />

              <label for="description">Description:</label>
              <input v-model="createForm.description" type="text" id="description" class="w-full p-1 border border-gray-300 rounded mb-2" />

              <label for="price">Price:</label>
              <input v-model="createForm.price" type="number" id="price" class="w-full p-1 border border-gray-300 rounded mb-4" />

              <label for="category">Category:</label>
              <select v-model="createForm.category" id="category" class="w-full p-1 border border-gray-300 rounded mb-4">
                <option disabled value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
              </select>

              <label for="image">Image:</label>
              <input @change="handleFileUpload" type="file" id="image" class="w-full p-1 border border-gray-300 rounded mb-0" />
            </p>
          </div>
          <!--footer-->
          <div class="flex items-center justify-end py-1 border-t border-solid border-blueGray-200 rounded-b">
            <button
              class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
              @click="isCreatingMenu = false"
            >
              Close
            </button>
            <button
              @click="createItem"
              class="text-emerald-500 background-transparent font-bold uppercase text-sm px-6 py-3 outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
            >
              Create
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup Editing Menu -->
    <div v-if="isEditingMenu" class="mt-6 overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="pr-4 relative max-w-xs mx-auto max-w-sm">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between px-6 py-2 border-b border-solid border-blueGray-200 rounded-t">
            <h3 class="text-xl font-semibold">Edit Menu</h3>
          </div>
          <!--body-->
          <div class="relative p-2 flex-auto">
            <p class="my-4 mx-4 text-blueGray-500 text-sm">
              <label for="name">Name:</label>
              <input v-model="editForm.name" type="text" id="name" class="w-full p-1 border border-gray-300 rounded mb-2" />

              <label for="description">Description:</label>
              <input v-model="editForm.description" type="text" id="description" class="w-full p-1 border border-gray-300 rounded mb-2" />

              <label for="price">Price:</label>
              <input v-model="editForm.price" type="number" id="price" class="w-full p-1 border border-gray-300 rounded mb-4" />

              <label for="category">Category:</label>
              <select v-model="editForm.category" id="category" class="w-full p-2 border border-gray-300 rounded mb-4">
                <option disabled value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.name">
                  {{ category.name }}
                </option>
              </select>
            </p>
          </div>
          <!--footer-->
          <div class="flex items-center justify-end py-1 border-t border-solid border-blueGray-200 rounded-b">
            <button
              class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
              @click="isEditingMenu = false"
            >
              Close
            </button>
            <button
              @click="saveItem"
              class="text-emerald-500 background-transparent font-bold uppercase text-sm px-6 py-3 outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
            >
              Save Change
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup Creating Category -->
    <div v-if="isCreatingCategory" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="relative w-auto mt-12 mx-auto max-w-sm">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between p-5 border-b border-solid border-blueGray-200 rounded-t">
            <h3 class="text-xl font-semibold">Create Category</h3>
          </div>
          <!--body-->
          <div class="relative p-6 flex-auto">
            <p class="my-4 mx-4 text-blueGray-500 text-lg leading-relaxed">
              <label class="mb-2" for="name">Name:</label>
              <input v-model="createCategoryForm.name" type="text" id="name" class="w-full p-2 border border-gray-300 rounded mb-2" />
            </p>
          </div>
          <!--footer-->
          <div class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
            <button
              class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
              @click="isCreatingCategory = false"
            >
              Close
            </button>
            <button
              @click="createCategory"
              class="text-emerald-500 background-transparent font-bold uppercase text-sm px-6 py-3 outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
            >
              Create
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup Editing Category -->
    <div v-if="isEditingCategory" class="overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center flex">
      <div class="relative w-auto mt-32 mx-auto max-w-sm">
        <!--content-->
        <div class="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
          <!--header-->
          <div class="flex items-start justify-between p-5 border-b border-solid border-blueGray-200 rounded-t">
            <h3 class="text-xl font-semibold">Edit Category</h3>
          </div>
          <!--body-->
          <div class="relative p-6 flex-auto">
            <p class="my-4 mx-4 text-blueGray-500 text-lg leading-relaxed">
              <label class="mb-2" for="categoryName">Name:</label>
              <input v-model="editCategoryForm.name" type="text" id="categoryName" class="w-full p-2 border border-gray-300 rounded mb-2" />
            </p>
          </div>
          <!--footer-->
          <div class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
            <button
              class="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
              @click="isEditingCategory = false"
            >
              Close
            </button>
            <button
              @click="saveCategory"
              class="text-emerald-500 background-transparent font-bold uppercase text-sm px-6 py-3 outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
              type="button"
            >
              Save Changes
            </button>
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
      menus: [],
      categories: [],
      searchQuery: "",
      isCreatingCategory: false,
      isCreatingMenu: false,
      isEditingMenu: false,
      isEditingCategory: false,

      createCategoryForm: {
        name: "",
      },

      editCategoryForm: {
        id: null,
        name: "",
      },

      editForm: {
        id: null,
        name: "",
        description: "",
        price: null,
        category: "",
      },
      createForm: {
        name: "",
        description: "",
        price: null,
        category: "",
        image: null,
      },
    };
  },
  components: {},
  computed: {
    groupedMenus() {
      const grouped = this.categories.reduce((acc, category) => {
        acc[category.name] = [];
        return acc;
      }, {});
      this.menus
        .filter(
          (menu) => menu.name.toLowerCase().startsWith(this.searchQuery.toLowerCase()) // Add this filter
        )
        .forEach((menu) => {
          if (!grouped[menu.category]) {
            grouped[menu.category] = [];
          }
          grouped[menu.category].push(menu);
        });
      return grouped;
    },
  },
  methods: {
    fetchCategories() {
      axios
        .get(`${this.$apiURL}/api/categories`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
          },
        })
        .then((response) => {
          this.categories = response.data.data;
        })
        .catch((error) => {
          console.error("Error fetching categories:", error);
        });
    },
    fetchMenus() {
      axios
        .get(`${this.$apiURL}/api/menus`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
          },
        })
        .then((response) => {
          this.menus = response.data;
        })
        .catch((error) => {
          console.error("Error fetching products:", error);
        });
    },
    editItem(item) {
      this.isEditingMenu = true;
      this.editForm = { ...item };
    },
    saveItem() {
      const category = this.categories.find((cat) => cat.name === this.editForm.category);

      if (category) {
        const updatedItem = {
          category_id: category.id, // Gunakan category_id yang didapat dari pencarian kategori
          name: this.editForm.name,
          description: this.editForm.description,
          price: this.editForm.price,
        };

        axios
          .put(`${this.$apiURL}/api/menus/${this.editForm.id}`, updatedItem, {
            headers: {
              "ngrok-skip-browser-warning": "69420",
              Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            },
          })
          .then(() => {
            this.$toast.success("Menu updated successfully");
            this.fetchMenus();
            this.isEditingMenu = false;
          })
          .catch((error) => {
            console.error("Error updating item:", error);
          });
      } else {
        console.error("Category not found");
      }
    },
    removeItem(item) {
      if (confirm(`Are you sure you want to delete ${item.name}?`)) {
        axios
          .delete(`${this.$apiURL}/api/menus/${item.id}`, {
            headers: {
              "ngrok-skip-browser-warning": "69420",
              Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            },
          })
          .then(() => {
            this.$toast.success("Item removed");
            this.fetchMenus();
          })
          .catch((error) => {
            console.error("Error deleting item:", error);
          });
      }
    },
    handleFileUpload(event) {
      const file = event.target.files[0];
      this.createForm.image = file;
    },
    createItem() {
      const formData = new FormData();

      formData.append("name", this.createForm.name);
      formData.append("description", this.createForm.description);
      formData.append("price", this.createForm.price);
      formData.append("category_id", this.createForm.category);
      formData.append("image", this.createForm.image);

      axios
        .post(`${this.$apiURL}/api/menus`, formData, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "Content-Type": "multipart/form-data",
          },
        })
        .then(() => {
          this.isCreatingMenu = false;
          this.fetchMenus();
        })
        .catch((error) => {
          console.error("Error creating item:", error);
        });
    },
    createCategory() {
      const newCategory = {
        name: this.createCategoryForm.name,
      };

      axios
        .post(`${this.$apiURL}/api/categories`, newCategory, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
        })
        .then(() => {
          this.fetchCategories(); // Refresh daftar kategori
          this.isCreatingCategory = false; // Tutup modal setelah berhasil
          this.createCategoryForm.name = ""; // Reset form
        })
        .catch((error) => {
          console.error("Error creating category:", error);
        });
    },
    editCategory(categoryName) {
      const category = this.categories.find((cat) => cat.name === categoryName);
      if (category) {
        this.isEditingCategory = true;
        this.editCategoryForm = { ...category };
        console.log(this.editCategoryForm); // Cek apakah data kategori sudah benar
      }
    },

    // Metode untuk menyimpan kategori setelah diedit
    saveCategory() {
      axios
        .put(`${this.$apiURL}/api/categories/${this.editCategoryForm.id}`, this.editCategoryForm, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`, // Tambahkan token otorisasi
          },
        })
        .then((response) => {
          console.log(response);
          this.fetchCategories();
          this.fetchMenus();
          this.isEditingCategory = false;
        })
        .catch((error) => {
          console.error("Error updating category:", error);
        });
    },

    // Metode untuk menghapus kategori
    removeCategory(categoryName) {
      const category = this.categories.find((cat) => cat.name === categoryName);
      if (category && confirm(`Are you sure you want to delete category ${category.name}?`)) {
        axios
          .delete(`${this.$apiURL}/api/categories/${category.id}`, {
            headers: {
              "ngrok-skip-browser-warning": "69420",
              Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            },
          })
          .then(() => {
            this.fetchCategories();
          })
          .catch((error) => {
            console.error("Error deleting category:", error);
          });
      }
    },
  },
  mounted() {
    this.fetchMenus();
    this.fetchCategories();
  },
};
</script>
