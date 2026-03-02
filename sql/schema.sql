CREATE DATABASE IF NOT EXISTS autostore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE autostore;

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO roles (name) VALUES ('admin'), ('user');

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  birth_date DATE NOT NULL,
  role_id INT NOT NULL,
  is_blocked TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);



INSERT INTO users (login, email, phone, password_hash, birth_date, role_id) VALUES
('Admin', 'admil@car.ru', '+7900 777 77 77', '$2y$12$p4s1sp9yvW/8tQVobltAV.YYFKk6ulyP3X3/5ZLv/faZ91Mv1./.C', '2006-09-27', (SELECT id FROM roles WHERE name='admin')),
('ivan_user', 'ivan@example.com', '+7900 111 11 11', '$2y$12$Nkd7PRNWy0eeGm766RrVzuz3JHkWUQgVF.LhaJ7zHZ8bSIUMQJEua', '2004-05-14', (SELECT id FROM roles WHERE name='user')),
('olga_user', 'olga@example.com', '+7900 222 22 22', '$2y$12$yaAn0TDqQKQTMZ8uigft4exGOKDF2EDNJHvLDhHpMjI6uSPNjxosy', '2003-11-03', (SELECT id FROM roles WHERE name='user'));

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  type ENUM('car', 'part') NOT NULL
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  type ENUM('car', 'part') NOT NULL,
  title VARCHAR(150) NOT NULL,
  short_description VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image_url VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  UNIQUE KEY uniq_cart_item(user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE order_statuses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO order_statuses (name) VALUES ('Новый'), ('В обработке'), ('Отправлен'), ('Завершён'), ('Отменён');

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  status_id INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  address VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (status_id) REFERENCES order_statuses(id)
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NOT NULL,
  admin_reply TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CHECK (rating BETWEEN 1 AND 5)
);

CREATE TABLE feedback_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  admin_reply TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO categories (name, type) VALUES
('Седаны', 'car'), ('Кроссоверы', 'car'), ('Двигатели', 'part'), ('Тормозная система', 'part');

INSERT INTO products (category_id, type, title, short_description, description, price, stock, image_url) VALUES
(1, 'car', 'AutoNova S1', 'Городской седан 1.6', 'Надёжный седан с экономичным расходом и современным оснащением.', 1890000, 4, 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=1200'),
(2, 'car', 'RoadX Crossover', 'Кроссовер 2.0 AWD', 'Полноприводный кроссовер для города и трассы, высокий клиренс.', 2790000, 3, 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1200'),
(3, 'part', 'Двигатель 1.8 Turbo', 'Турбированный мотор', 'Новый двигатель с повышенной мощностью и ресурсом.', 420000, 10, 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=1200'),
(4, 'part', 'Тормозные колодки ProStop', 'Комплект передних колодок', 'Колодки для эффективного торможения и низкого износа.', 8500, 50, 'https://images.unsplash.com/photo-1613214150384-034f74f5105c?w=1200');
