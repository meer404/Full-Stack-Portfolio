CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value_en TEXT,
  setting_value_ku TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE about (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_en VARCHAR(150),
  name_ku VARCHAR(150),
  bio_en TEXT,
  bio_ku TEXT,
  profile_image VARCHAR(255),
  university_en VARCHAR(200),
  university_ku VARCHAR(200),
  graduation_year VARCHAR(10),
  email VARCHAR(150),
  phone VARCHAR(50),
  github_url VARCHAR(255),
  linkedin_url VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE resume (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cv_file VARCHAR(255),
  cv_filename_display VARCHAR(150),
  section_title_en VARCHAR(100) DEFAULT 'Resume',
  section_title_ku VARCHAR(100) DEFAULT 'ڕیزومێ',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE resume_skills (
  id INT AUTO_INCREMENT PRIMARY KEY,
  skill_name_en VARCHAR(100),
  skill_name_ku VARCHAR(100),
  skill_level INT DEFAULT 80,
  category_en VARCHAR(80),
  category_ku VARCHAR(80),
  sort_order INT DEFAULT 0
);

CREATE TABLE resume_experience (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title_en VARCHAR(200),
  title_ku VARCHAR(200),
  company_en VARCHAR(200),
  company_ku VARCHAR(200),
  date_range_en VARCHAR(100),
  date_range_ku VARCHAR(100),
  description_en TEXT,
  description_ku TEXT,
  sort_order INT DEFAULT 0
);

CREATE TABLE resume_education (
  id INT AUTO_INCREMENT PRIMARY KEY,
  degree_en VARCHAR(200),
  degree_ku VARCHAR(200),
  institution_en VARCHAR(200),
  institution_ku VARCHAR(200),
  year_range VARCHAR(50),
  description_en TEXT,
  description_ku TEXT,
  sort_order INT DEFAULT 0
);

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title_en VARCHAR(200),
  title_ku VARCHAR(200),
  description_en TEXT,
  description_ku TEXT,
  thumbnail VARCHAR(255),
  demo_url VARCHAR(255),
  github_url VARCHAR(255),
  tags VARCHAR(500),
  is_featured TINYINT(1) DEFAULT 0,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_name VARCHAR(150),
  sender_email VARCHAR(150),
  subject VARCHAR(250),
  message TEXT,
  is_read TINYINT(1) DEFAULT 0,
  received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$KmKv.x95kLEjogCLAWUNOOh7ksEx2WuYw2Qy5cmu581CRmmjmMKMe');

INSERT INTO settings (setting_key, setting_value_en, setting_value_ku) VALUES
('site_name', 'Student Portfolio', 'پۆرتفۆلیۆی خوێندکار'),
('meta_description', 'Full-stack portfolio for a CS student.', 'پۆرتفۆلیۆی فولستاک بۆ خوێندکاری زانستی کۆمپیوتەر.'),
('hero_greeting', "Hello, I'm", 'سڵاو، ناوم'),
('hero_tagline', 'Building bilingual, modern web experiences.', 'تەکنەلۆژیای نوێ و وێبی دووزمانە دروست دەکەم.');

INSERT INTO about (name_en, name_ku, bio_en, bio_ku, profile_image, university_en, university_ku, graduation_year, email, phone, github_url, linkedin_url)
VALUES
('Your Name', 'ناوی تۆ', 'I am a computer science student focused on full-stack development, UI/UX, and clean architecture.', 'من خوێندکاری زانستی کۆمپیوتەرم و سەرنجی من لەسەر فولستاک، دیزاینی UI/UX و شێوازی پاکە.', '/portfolio/uploads/profile/default.svg', 'Your University', 'زانکۆی تۆ', '2026', 'you@email.com', '+964 000 0000', 'https://github.com/', 'https://linkedin.com/');

INSERT INTO resume (cv_file, cv_filename_display) VALUES ('', 'CV.pdf');

INSERT INTO resume_skills (skill_name_en, skill_name_ku, skill_level, category_en, category_ku, sort_order) VALUES
('PHP', 'PHP', 85, 'Backend', 'بەکەند', 1),
('MySQL', 'MySQL', 80, 'Database', 'داتابەیس', 2),
('JavaScript', 'JavaScript', 78, 'Frontend', 'فرۆنتئەند', 3),
('Tailwind CSS', 'Tailwind CSS', 82, 'Frontend', 'فرۆنتئەند', 4);

INSERT INTO resume_experience (title_en, title_ku, company_en, company_ku, date_range_en, date_range_ku, description_en, description_ku, sort_order) VALUES
('Web Developer Intern', 'ئینتێرنێتی گەشەپێدەری وێب', 'Tech Lab', 'تێک لاب', '2024 - Present', '٢٠٢٤ - ئێستا', 'Building responsive web apps with PHP and Tailwind.', 'ئەپەکانی وێبی وەڵامدانەوە دروست دەکەم بە PHP و Tailwind.', 1);

INSERT INTO resume_education (degree_en, degree_ku, institution_en, institution_ku, year_range, description_en, description_ku, sort_order) VALUES
('B.Sc. Computer Science', 'بەکالۆریۆس زانستی کۆمپیوتەر', 'Your University', 'زانکۆی تۆ', '2022 - 2026', 'Focus on software engineering and web systems.', 'سەرنج لەسەر ئەندازیاری سۆفتوێر و سیستەمی وێب.', 1);

INSERT INTO projects (title_en, title_ku, description_en, description_ku, thumbnail, demo_url, github_url, tags, is_featured, sort_order) VALUES
('Smart Campus Portal', 'پۆرتاڵی زانکۆی زیرەک', 'A full-stack portal for students with schedules and messaging.', 'پۆرتاڵێکی فولستاک بۆ خوێندکاران بە کاتژمێر و پەیام.', '/portfolio/uploads/projects/default.svg', '#', '#', 'PHP,MySQL,Tailwind', 1, 1),
('Task Tracker', 'شوێنکەری ئەرکەکان', 'Simple productivity app with tag filters.', 'ئەپێکی سادە بۆ بەرهەمەری و فلتەری تاگ.', '/portfolio/uploads/projects/default.svg', '#', '#', 'JavaScript,UI/UX', 0, 2);
