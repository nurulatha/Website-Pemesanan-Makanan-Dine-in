<template>
  <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded" :class="[color === 'light' ? 'bg-white' : 'bg-emerald-900 text-white']">
    <div class="rounded-t mb-0 px-4 py-3 border-0">
      <div class="flex flex-wrap items-center">
        <div class="relative w-full px-4 max-w-full flex-grow flex-1">
          <h3 class="font-semibold text-lg" :class="[color === 'light' ? 'text-blueGray-700' : 'text-white']">All Menus</h3>
        </div>
      </div>
    </div>

    <div class="block w-full overflow-x-auto">
      <button @click="isCreatingMenu = true" class="Menu bg-slate-300 hover:bg-slate-800 hover:text-white p-2 mb-9"><i class="fa-solid fa-plus mr-2"></i> Create Menu</button>
      <button @click="isCreatingCategory = true" class="Menu bg-slate-300 hover:bg-slate-800 hover:text-white p-2 mb-9 ml-5"><i class="fa-solid fa-plus mr-2"></i>Create Category</button>
      <!-- Projects table -->
      <table class="items-center w-full bg-transparent border-collapse">
        <thead>
          <tr>
            <th class="border border-slate-300 p-2">No</th>
            <th class="border border-slate-300 p-2">Category</th>
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
            <td class="border border-slate-300 p-2">{{ item.category }}</td>
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
    <div v-if="isEditingMenu" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Edit Menu</h3>
        <label for="name">Name:</label>
        <input v-model="editForm.name" type="text" id="name" class="w-full p-2 border border-gray-300 rounded mb-2" />

        <label for="description">Description:</label>
        <input v-model="editForm.description" type="text" id="description" class="w-full p-2 border border-gray-300 rounded mb-2" />

        <label for="price">Price:</label>
        <input v-model="editForm.price" type="number" id="price" class="w-full p-2 border border-gray-300 rounded mb-4" />

        <label for="category">Category:</label>
        <select v-model="editForm.category" id="category" class="w-full p-2 border border-gray-300 rounded mb-4">
          <option disabled value="">Select a category</option>
          <option v-for="category in categories" :key="category.id" :value="category.name">
            {{ category.name }}
          </option>
        </select>

        <div class="flex justify-end space-x-4">
          <button @click="isEditingMenu = false" class="text-red-500">Cancel</button>
          <button @click="saveItem" class="text-blue-500">Save</button>
        </div>
      </div>
    </div>
    <div v-if="isEditingCategory" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Edit Category</h3>
        <label class="mb-2" for="categoryName">Name:</label>
        <input v-model="editCategoryForm.name" type="text" id="categoryName" class="w-full p-2 border border-gray-300 rounded mb-2" />
        <div class="flex justify-end space-x-4">
          <button @click="isEditingCategory = false" class="text-red-500">Cancel</button>
          <button @click="saveCategory" class="text-blue-500">Save</button>
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
  props: {
    color: {
      default: "light",
      validator: function (value) {
        // The value must match one of these strings
        return ["light", "dark"].indexOf(value) !== -1;
      },
    },
  },
  computed: {
    groupedMenus() {
      const grouped = this.categories.reduce((acc, category) => {
        acc[category.name] = [];
        return acc;
      }, {});
      this.menus.forEach((menu) => {
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
        this.editCategoryForm = { ...category }; // Menyalin data termasuk id
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
          this.fetchCategories(); // Refresh daftar kategori setelah update
          this.isEditingCategory = false; // Tutup modal setelah simpan
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
