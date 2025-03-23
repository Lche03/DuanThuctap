<template>
    <div class="container mx-auto p-6 space-y-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">
                Danh sách công việc
            </h1>
            <button @click="logout"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 font-semibold">
                Đăng xuất
            </button>
        </div>

        <!-- Thông báo -->
        <div v-show="notification"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ notification }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="notification = null">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20">
                    <path
                        d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 10-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z" />
                </svg>
            </span>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="p-4 font-semibold">Tên công việc</th>
                        <th class="p-4 font-semibold">Người được giao</th>
                        <th class="p-4 font-semibold">Trạng thái</th>
                        <th class="p-4 font-semibold">Hạn chót</th>
                        <th class="p-4 font-semibold">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks" :key="task.id" class="border-t hover:bg-gray-50 transition duration-150">
                        <td class="p-4">{{ task.title }}</td>
                        <td class="p-4">{{ getUserName(task.assignee_id) }}</td>
                        <td class="p-4">
                            <select v-if="isAssignee(task.assignee_id)" v-model="task.status"
                                @change="updateTaskStatus(task)"
                                class="px-2 py-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Chưa bắt đầu">
                                    Chưa bắt đầu
                                </option>
                                <option value="Đang thực hiện">
                                    Đang thực hiện
                                </option>
                                <option value="Đã hoàn thành">
                                    Đã hoàn thành
                                </option>
                            </select>
                            <span v-else :class="statusClass(task.status)" class="font-medium">
                                {{ task.status }}
                            </span>
                        </td>
                        <td class="p-4">{{ task.due_date }}</td>
                        <td class="p-4 flex space-x-2">
                            <button v-if="isAdmin" @click="editTask(task)"
                                class="text-blue-500 hover:text-blue-700 font-medium transition duration-200">
                                Sửa
                            </button>
                            <button v-if="isAdmin" @click="deleteTask(task.id)"
                                class="text-red-500 hover:text-red-700 font-medium transition duration-200">
                                Xóa
                            </button>
                        </td>
                    </tr>
                    <tr v-if="tasks.length === 0">
                        <td colspan="5" class="p-4 text-center text-gray-500">
                            Chưa có công việc nào
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="isAdmin" class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                Thêm công việc mới
            </h2>
            <form @submit.prevent="addTask" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input v-model="newTask.title" placeholder="Tên công việc"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        required />
                    <input v-model="newTask.due_date" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        required />
                    <select v-model="newTask.assignee_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        required>
                        <option value="" disabled>Chọn người giao</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>
                <div v-if="errorMessage" class="text-red-500 text-sm text-center">
                    {{ errorMessage }}
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
                    :disabled="isLoading">
                    {{ isLoading ? "Đang thêm..." : "Thêm công việc" }}
                </button>
            </form>
        </div>
        <div v-else class="text-gray-600 text-center">
            Chỉ admin mới có thể thêm công việc.
        </div>

        <div v-if="editingTask" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    Sửa công việc
                </h2>
                <form @submit.prevent="saveTask" class="space-y-4">
                    <input v-model="editingTask.title" placeholder="Tên công việc"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        required />
                    <input v-model="editingTask.due_date" type="date"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        required />
                    <select v-model="editingTask.status"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        required>
                        <option value="Chưa bắt đầu">Chưa bắt đầu</option>
                        <option value="Đang thực hiện">Đang thực hiện</option>
                        <option value="Đã hoàn thành">Đã hoàn thành</option>
                    </select>
                    <div class="flex space-x-4">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
                            :disabled="isLoading">
                            Lưu
                        </button>
                        <button @click="editingTask = null"
                            class="w-full bg-gray-300 text-gray-800 py-3 rounded-lg font-semibold hover:bg-gray-400 transition duration-200">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Pusher from "pusher-js";

export default {
    data() {
        return {
            tasks: [],
            users: [],
            newTask: { title: "", due_date: "", assignee_id: "" },
            editingTask: null,
            errorMessage: "",
            isLoading: false,
            isAdmin: localStorage.getItem("role") === "admin",
            currentUserId: null, // Sẽ được cập nhật sau khi đăng nhập
            notification: null,
        };
    },
    mounted() {
        // Lấy user từ localStorage
        const user = JSON.parse(localStorage.getItem("user"));
        console.log(user);
        if (user) {
            this.currentUserId = user.id;
            console.log("Current user ID:", this.currentUserId);

        }
        this.fetchTasks();
        this.fetchUsers();
        if (this.isAdmin) {
            this.fetchUsers();
        }

        Pusher.logToConsole = true;
        const pusher = new Pusher("c59e8a8c8980e404fb73", {
            cluster: "ap1",
            forceTLS: true,
            authEndpoint: "/broadcasting/auth", // Endpoint xác thực
            auth: {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            },
        });
        const channel = pusher.subscribe("tasks");

        pusher.connection.bind("connected", function () {
            console.log("✅ Pusher connected!");
        });
        channel.bind_global((eventName, data) => {
            console.log(`📢 Nhận sự kiện: ${eventName}`, data);
        });
        const vm = this;

        channel.bind("task.added", function (data) {
            console.log("[Pusher] Công việc mới được thêm:", data);
            vm.showNotification({
                assignee_id: data.assignee_id,
                title: data.title
            }, "added");
        });

        channel.bind("task.updated", function (data) {
            console.log("[Pusher] Công việc được cập nhật:", data);
            vm.showNotification({
                assignee_id: data.assignee_id,
                title: data.title
            }, "updated");
        });
    },
    methods: {
        async fetchTasks() {
            this.$axios.defaults.headers.common[
                "Authorization"
            ] = `Bearer ${localStorage.getItem("token")}`;
            const response = await this.$axios.get("/api/tasks");
            this.tasks = response.data;
        },
        async fetchUsers() {
            this.$axios.defaults.headers.common[
                "Authorization"
            ] = `Bearer ${localStorage.getItem("token")}`;
            const response = await this.$axios.get("/api/users");
            this.users = response.data;
        },
        async addTask() {
            this.errorMessage = "";
            this.isLoading = true;

            try {
                await this.$axios.post("/api/tasks", this.newTask);
                this.fetchTasks();
                this.newTask.title = "";
                this.newTask.due_date = "";
                this.newTask.assignee_id = "";
            } catch (error) {
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.errorMessage = error.response.data.message;
                } else {
                    this.errorMessage = "Đã xảy ra lỗi khi thêm công việc.";
                }
            } finally {
                this.isLoading = false;
            }
        },
        async updateTaskStatus(task) {
            this.isLoading = true;
            try {
                await this.$axios.put(`/api/tasks/${task.id}`, {
                    status: task.status,
                });
                this.fetchTasks();
            } catch (error) {
                this.errorMessage =
                    error.response?.data?.message ||
                    "Đã xảy ra lỗi khi cập nhật trạng thái.";
            } finally {
                this.isLoading = false;
            }
        },
        async saveTask() {
            this.isLoading = true;
            try {
                await this.$axios.put(
                    `/api/tasks/${this.editingTask.id}`,
                    this.editingTask
                );
                this.fetchTasks();
                this.editingTask = null;
            } catch (error) {
                this.errorMessage =
                    error.response?.data?.message ||
                    "Đã xảy ra lỗi khi lưu công việc.";
            } finally {
                this.isLoading = false;
            }
        },
        async deleteTask(id) {
            if (confirm("Bạn có chắc muốn xóa công việc này?")) {
                await this.$axios.delete(`/api/tasks/${id}`);
                this.fetchTasks();
            }
        },
        async logout() {
            await this.$axios.post("/api/logout");
            localStorage.removeItem("token");
            localStorage.removeItem("role");
            localStorage.removeItem("user");
            this.$router.push("/login");
        },
        showNotification(data, type) {
            console.log("Dữ liệu thông báo:", data);
            console.log("ID người dùng hiện tại:", this.currentUserId);

            if (!data || !data.assignee_id || !this.currentUserId) {
                console.error(
                    "Thiếu dữ liệu hoặc ID người dùng",
                    data,
                    this.currentUserId
                );
                return;
            }

            if (data.assignee_id.toString() === this.currentUserId.toString()) {
                const messages = {
                    added: `Bạn được giao công việc mới: ${data.title}`,
                    updated: `Cập nhật trạng thái: ${data.title}`,
                };

                if (!messages[type]) {
                    console.error("Loại thông báo không hợp lệ:", type);
                    return;
                }

                this.notification = messages[type];
                setTimeout(() => {
                    this.notification = null;
                }, 5000);
            }

            this.fetchTasks();
        },
        editTask(task) {
            this.editingTask = { ...task };
        },
        isAssignee(assigneeId) {
            return this.currentUserId === assigneeId;
        },
        getUserName(userId) {
            const user = this.users.find((user) => user.id === userId);
            return user ? user.name : "Không xác định";
        },
        statusClass(status) {
            return {
                "text-yellow-600": status === "Chưa bắt đầu",
                "text-blue-600": status === "Đang thực hiện",
                "text-green-600": status === "Đã hoàn thành",
            };
        },
    },
};
</script>
