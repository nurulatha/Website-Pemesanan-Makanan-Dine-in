<template>
  <div class="w-full overflow-x-auto blocl">
    <h1 class="text-xl text-black">Report Table's Sale</h1>
    <div class="flex mt-5">
      <input type="date" v-model="startDate" />
      <input type="date" v-model="endDate" class="mr-3" />
      <button @click="fetchTableReport" class="px-6 text-white rounded bg-emerald-500 hover:shadow-lg">Filter</button>
      <button @click="exportToExcel" class="px-6 ml-3 bg-transparent border-2 rounded text-emerald-600 hover:shadow-lg" title="download excel">Excel</button>
      <button @click="exportToPDF" class="px-6 text-red-500 bg-transparent border-2 rounded hover:shadow-lg" title="download pdf">PDF</button>
    </div>
    <table id="salesTable" class="items-center w-full mt-6 bg-white border-collapse">
      <thead>
        <tr class="bg-gray-200 border">
          <th class="p-2 border border-slate-300">Table</th>
          <th class="p-2 border border-slate-300">Total Order</th>
          <th class="p-2 border border-slate-300">Sales</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th class="p-2 border border-slate-300">Total</th>
          <th class="p-2 border border-slate-300"></th>
          <!-- Placeholder untuk total orders -->
          <th class="p-2 border border-slate-300"></th>
          <!-- Placeholder untuk total sales jika dibutuhkan -->
        </tr>
      </tfoot>
      <tbody>
        <tr v-for="report in reports" :key="report.table_id" class="border border-slate-300">
          <td class="p-2 border border-slate-300">{{ report.table_id }}</td>
          <td class="p-2 border border-slate-300">{{ report.total_orders }} order</td>
          <td class="p-2 border border-slate-300">Rp {{ report.total_sales }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import "datatables.net";

export default {
  data() {
    return {
      reports: [],
      startDate: null,
      endDate: null,
      dataTable: null,
    };
  },
  methods: {
    fetchTableReport() {
      const params = {};
      if (this.startDate) {
        params.start_date = this.startDate;
      }
      if (this.endDate) {
        params.end_date = this.endDate;
      }
      axios
        .get(`${this.$apiURL}/api/reports/tables`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
          params: params,
        })
        .then((response) => {
          this.reports = response.data.data;
          this.initializeDataTable();
        })
        .catch((error) => {
          console.error("error fetching report", error);
        });
    },
    exportToExcel() {
      const params = {};
      if (this.startDate) {
        params.start_date = this.startDate;
      }
      if (this.endDate) {
        params.end_date = this.endDate;
      }

      axios
        .get(`${this.$apiURL}/api/reports/tables/excel`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
          params: params,
          responseType: "blob", // Mengunduh file sebagai Blob (Excel file)
        })
        .then((response) => {
          // Buat URL object untuk file Excel
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "table_sales_report.xlsx"); // Nama file yang diunduh
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch((error) => {
          console.error("Error exporting to Excel:", error);
        });
    },

    exportToPDF() {
      const params = {};
      if (this.startDate) {
        params.start_date = this.startDate;
      }
      if (this.endDate) {
        params.end_date = this.endDate;
      }

      axios
        .get(`${this.$apiURL}/api/reports/tables/pdf`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
          params: params,
          responseType: "blob", // Mengunduh file sebagai Blob (PDF file)
        })
        .then((response) => {
          // Buat URL object untuk file PDF
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "table_sales_report.pdf"); // Nama file yang diunduh
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch((error) => {
          console.error("Error exporting to PDF:", error);
        });
    },
    initializeDataTable() {
      // Destroy the previous instance if it exists
      if ($.fn.DataTable.isDataTable("#salesTable")) {
        $("#salesTable").DataTable().destroy();
      }

      // Initialize DataTable
      this.dataTable = $("#salesTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        order: [[0, "asc"]],
        data: this.reports, // Use the fetched reports
        columns: [
          { data: "table_id" },
          {
            data: "total_orders",
            render: (data) => `${data} order`, // Menambahkan "order" setelah total_orders
          },
          {
            data: "total_sales",
            render: (data) => `Rp ${data}`,
          },
        ],
        createdRow: function (row) {
          // Adding custom classes for Tailwind CSS
          $(row).addClass("bg-white hover:bg-gray-100 text-md");
          $("td", row).addClass("border border-slate-300 p-2");
        },
        headerCallback: function (thead) {
          // Adding custom classes for Tailwind CSS
          $("th", thead).addClass("border border-slate-300 p-2");
        },
        // Tambahkan footerCallback untuk menghitung total orders
        footerCallback: function () {
          let api = this.api();

          // Calculate the total for the current page (visible rows)
          let totalOrders = api
            .column(1, { page: "current" })
            .data()
            .reduce((a, b) => a + parseInt(b), 0);

          let totalSales = api
            .column(2, { page: "current" }) // Kolom ke-2 (total_sales)
            .data()
            .reduce((a, b) => a + parseFloat(b), 0);

          // Update the footer
          $(api.column(1).footer()).html(`${totalOrders} orders`);
          $(api.column(2).footer()).html(`Rp ${totalSales.toLocaleString("id-ID")}`);
        },
      });
    },
  },
  mounted() {
    this.fetchTableReport();
  },
};
</script>
