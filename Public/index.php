<?php
// Подключаем файл базы
require __DIR__ . '/../server/db/database.php';

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мониторинг станций</title>
    <!-- Vue 3 CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Vue Router 4 -->
    <script src="https://unpkg.com/vue-router@4/dist/vue-router.global.js"></script>

    <!-- Axios для HTTP запросов -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tailwind DELETE -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="stylesheet" href="main.css">
    <style>
    [v-cloak] {
        display: none;
    }

    .container-fluid {
        position: relative;
        overflow: auto;
        /* height: 100vh; */
    }

    .router-link-exact-active {
        background-color: #5183b9ff;
        color: white !important;
        font-weight: bold;
    }

    h4 {
        margin-bottom: 0;
    }

    p {
        margin-bottom: 0;
    }

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.3s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }

    .fade-enter-to,
    .fade-leave-from {
        opacity: 1;
    }
    </style>
</head>

<body>

    <section id="app" v-cloak class="container-fluid bg-white p-4 my-5 mx-auto shadow-lg" style="max-width: 1800px;">
        <header class="bg-gray-300 p-6 mb-3">
            <h3 class="text-center mb-4">Тут будет навигация: </h3>
            <div class="flex items-center justify-between">
                <router-link to="/smenaHour" class="nav-link">
                    <h4>Смена (час)</h4>
                </router-link>
                <router-link to="/smenaDay" class="nav-link">
                    <h4>Смена (день)</h4>
                </router-link>
                <router-link to="/dispatch" class="nav-link">
                    <h4>Диспетчерская</h4>
                </router-link>
                <router-link to="/data" class="nav-link">
                    <h4>Данные</h4>
                </router-link>
                <router-link to="/reports" class="nav-link">
                    <h4>Отчеты</h4>
                </router-link>
                <router-link to="/plan" class="nav-link">
                    <h4>План</h4>
                </router-link>
                <router-link to="/" class="nav-link">
                    <h4>Станции (данные)</h4>
                </router-link>
            </div>
        </header>
        <!-- Vue приложение В ЭТУ ШЛЯПУ ПОТОМ БУДЕМ ДЕЛАТЬ РОУТИНГ И ПОДКЛЮЧАТЬ КОМПОНЕНТЫ -->
        <main class="wrapper bg-white p-2 ">
            <!-- Сюда будет рендерится странички с роутера  -->
            <router-view></router-view>
            <!-- Компонент СТАНЦИй -->
        </main>
    </section>

    <section id="modalRoot"></section>





    <script type="module">
    import OtherComponent from './OtherComponent.js';
    import StationPage from './pages/StationPage.js';

    const {
        createApp
    } = Vue;

    const {
        createRouter,
        createWebHashHistory
    } = VueRouter;



    const Home = {
        template: `
            <div class="container-fluid bg-white p-4 mt-3 mx-auto shadow-lg">
                <h2>Добро пожаловать в систему мониторинга</h2>
                <p>Здесь будет информация о системе</p>
            </div>
        `
    };

    // Компонент Настроек
    const SettingsComponent = {
        template: `
            <div class="container-fluid bg-white p-4 mt-3 mx-auto shadow-lg">
                <h2>Настройки системы</h2>
                <p>Здесь будут настройки мониторинга</p>
            </div>
        `
    };



    const app = createApp({
        //* Локальная регистрация Компонента
        components: {
            'other-component': OtherComponent,
        },

    });

    const routes = [{
        path: '/',
        component: StationPage
    }, {
        path: '/plan',
        component: SettingsComponent
    }];

    // Создание роутера
    const router = createRouter({
        history: createWebHashHistory('/Public/'),
        routes
    });

    // Глобальная регистрация
    // app.component("other-component", OtherComponent);
    app.use(router);
    app.mount('#app');
    </script>
</body>

</html>