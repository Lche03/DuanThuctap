<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
                Đăng ký
            </h1>
            <form @submit.prevent="register" class="space-y-6">
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-gray-700"
                    >Tên</label>
                    <input
                        v-model="name"
                        type="text"
                        id="name"
                        placeholder="Nhập tên của bạn"
                        class="mt-1 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    />
                </div>
                <div>
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700"
                    >Email</label>
                    <input
                        v-model="email"
                        type="email"
                        id="email"
                        placeholder="Nhập email của bạn"
                        class="mt-1 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    />
                </div>
                <div>
                    <label
                        for="password"
                        class="block text-sm font-medium text-gray-700"
                    >Mật khẩu</label>
                    <input
                        v-model="password"
                        type="password"
                        id="password"
                        placeholder="Nhập mật khẩu"
                        class="mt-1 w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    />
                </div>
                <!-- Hiển thị lỗi nếu có -->
                <div
                    v-if="errorMessage"
                    class="text-red-500 text-sm text-center"
                >
                    {{ errorMessage }}
                </div>
                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
                    :disabled="isLoading"
                >
                    {{ isLoading ? "Đang đăng ký..." : "Đăng ký" }}
                </button>
            </form>
            <!-- Link đăng nhập -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    Đã có tài khoản?
                    <button
                        @click="$router.push('/login')"
                        class="text-blue-600 hover:underline font-medium"
                        :disabled="isLoading"
                    >
                        {{ isLoading ? "Đang xử lý..." : "Đăng nhập" }}
                    </button>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name: "",
            email: "",
            password: "",
            errorMessage: "",
            isLoading: false,
        };
    },
    methods: {
        async register() {
            this.errorMessage = "";
            this.isLoading = true;

            try {
                const response = await this.$axios.post("/api/register", {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                });
                localStorage.setItem("token", response.data.token);
                this.$router.push("/login");
            } catch (error) {
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.errorMessage = error.response.data.message;
                } else {
                    this.errorMessage =
                        "Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.";
                }
            } finally {
                this.isLoading = false;
            }
        },
    },
};
</script>