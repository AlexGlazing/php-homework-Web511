--создание структуры таблиц
CREATE TABLE IF NOT EXISTS "categories"
(
    "id"          INTEGER NOT NULL,
    "name"        VARCHAR NOT NULL UNIQUE,
    "slug"        VARCHAR NOT NULL UNIQUE,
    "description" TEXT,
    PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "posts"
(
    "id"          INTEGER NOT NULL,
    "category_id" INTEGER,
    "title"       VARCHAR NOT NULL,
    "content"     TEXT,
    "date"        VARCHAR,
    "author"      VARCHAR,
    "image"       VARCHAR,
    PRIMARY KEY ("id")
);

-- Лайки к постам.
-- Поле user_id хранит "идентификатор лайкера":
--   • Для анонимных посетителей — значение куки uid (случайная строка ~32 hex-символа).
--   • Для авторизованных пользователей (после регистрации и входа) — "user:<id>",
--     где <id> — это primary key из таблицы users (например: "user:7").
-- Благодаря этому у каждого зарегистрированного пользователя свои независимые лайки,
-- даже если они используют один и тот же браузер. Старые анонимные лайки остаются рабочими.
CREATE TABLE IF NOT EXISTS "likes"
(
    "post_id" INTEGER NOT NULL,
    "user_id" VARCHAR NOT NULL,
    PRIMARY KEY ("post_id", "user_id")
);

CREATE TABLE IF NOT EXISTS "users"
(
    "id"            INTEGER NOT NULL,
    "nickname"      VARCHAR NOT NULL UNIQUE,
    "email"         VARCHAR NOT NULL UNIQUE,
    "password_hash" TEXT NOT NULL,
    "created_at"    DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("id")
);

--seeding заполнение данными таблиц
INSERT OR IGNORE INTO categories (id, name, slug, description) VALUES (1, 'Frontend', 'frontend', 'HTML, CSS, JavaScript и современные фронтенд технологии');
INSERT OR IGNORE INTO categories (id, name, slug, description) VALUES (2, 'Backend', 'backend', 'Серверная разработка, базы данных, API и архитектура');
INSERT OR IGNORE INTO categories (id, name, slug, description) VALUES (3, 'DevOps', 'devops', 'Контейнеризация, CI/CD, облачные технологии и автоматизация');
INSERT OR IGNORE INTO categories (id, name, slug, description) VALUES (4, 'Мобильная разработка', 'mobile-dev', 'iOS, Android, Flutter, React Native и кроссплатформенная разработка');
INSERT OR IGNORE INTO categories (id, name, slug, description) VALUES (5, 'Базы данных', 'databases', 'SQL, NoSQL, оптимизация запросов и управление данными');

INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (4, 3, 'Vite вместо Webpack: стоит ли переходить 2', 'Сравнение скорости сборки, конфигурации и экосистемы двух популярных сборщиков.', '2024-03-20', 'Дмитрий', 'public/upload/6a12d260b027c_2026-05-24_10-26-40.jpg');
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (5, 2, 'Асинхронность в Python: asyncio vs многопоточность', 'Когда использовать async/await, а когда threading — разбор с примерами кода.', '2024-02-05', 'Олег', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (6, 2, 'Чистая архитектура в Go', 'Реализация слоев: сущности, use cases, репозитории и контроллеры на примере REST API.', '2024-02-28', 'Сергей', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (7, 2, 'Node.js: обработка ошибок в асинхронных функциях', 'Трюки с try/catch, доменные ошибки и глобальные обработчики для надежного кода.', '2024-03-15', 'Алина', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (8, 2, 'REST против GraphQL: реальное сравнение', 'Плюсы и минусы подходов на проектах средней и высокой нагрузки.', '2024-04-01', 'Роман', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (9, 3, 'Docker Compose для локального окружения', 'Как поднять бэкенд, базу, кэш и очередь за 5 минут — готовый docker-compose.yml.', '2024-01-25', 'Игорь', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (10, 3, 'GitHub Actions: полный гайд для CI/CD', 'Сборка, тестирование и деплой на примере React + Node.js приложения.', '2024-02-18', 'Виктория', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (11, 3, 'Kubernetes для начинающих', 'Поды, деплойменты, сервисы и ингрессы — минимальный набор для старта.', '2024-03-10', 'Алексей', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (12, 3, 'Terraform: управление облачной инфраструктурой как кодом', 'Провайдеры, ресурсы, переменные и состояния — пример для AWS.', '2024-04-05', 'Павел', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (13, 4, 'Flutter 3: что нового', 'Поддержка веба и десктопа, улучшенная производительность и обновленные виджеты.', '2024-01-18', 'Маргарита', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (14, 4, 'SwiftUI против UIKit в 2024', 'Когда стоит использовать SwiftUI, а где без UIKit не обойтись.', '2024-02-22', 'Константин', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (15, 4, 'React Native: оптимизация производительности', 'Мемоизация, FlatList, нативные модули и профайлинг.', '2024-03-25', 'Юлия', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (16, 4, 'Kotlin Multiplatform Mobile: первый опыт', 'Шаринг бизнес-логики между iOS и Android без потери нативной производительности.', '2024-04-10', 'Артем', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (17, 5, 'Индексы в PostgreSQL: как не ошибиться', 'B-tree, Hash, GIN, BRIN — какой индекс подходит для ваших запросов.', '2024-01-30', 'Татьяна', NULL);
INSERT OR IGNORE INTO posts (id, category_id, title, content, date, author, image) VALUES (18, 5, 'MongoDB: агрегации на примере реальных задач', '$match, $group, $lookup — как заменить сложные SQL-запросы в NoSQL.', '2024-02-14', 'Никита', NULL);

-- likes (только для существующих постов)
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (4, '4a9cbuikq6fc19hlo55odo9e1r');
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (4, '90bh6g3oeusomlbikhs7vgks9v');
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (4, 'thp7efbacppvkf008abrv8ubdl');
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (6, 'thp7efbacppvkf008abrv8ubdl');
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (7, 'thp7efbacppvkf008abrv8ubdl');
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (8, 'thp7efbacppvkf008abrv8ubdl');
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (11, 'thp7efbacppvkf008abrv8ubdl');
INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (13, 'thp7efbacppvkf008abrv8ubdl');
