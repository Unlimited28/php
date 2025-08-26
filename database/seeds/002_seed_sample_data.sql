-- Sample seed data for testing
-- Create test users with proper unique IDs

-- Insert sample users with hashed passwords (password: 'password123')
INSERT INTO users (unique_id, full_name, email, phone, password, role, association_id, rank_id, church, age, status) VALUES
('OGBC/RA/0001', 'John Doe', 'john.doe@example.com', '+234 801 234 5678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ambassador', 1, 1, 'First Baptist Church, Abeokuta', 16, 'active'),
('OGBC/RA/0002', 'Jane Smith', 'jane.smith@example.com', '+234 802 345 6789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'president', 1, 9, 'Central Baptist Church, Abeokuta', 35, 'active'),
('OGBC/RA/0003', 'Admin User', 'admin@ogbc.org', '+234 803 456 7890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NULL, 11, NULL, NULL, 'active'),
('OGBC/RA/0004', 'Mary Johnson', 'mary.johnson@example.com', '+234 804 567 8901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ambassador', 2, 3, 'Emmanuel Baptist Church, Ijebu', 18, 'active'),
('OGBC/RA/0005', 'David Wilson', 'david.wilson@example.com', '+234 805 678 9012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'president', 3, 8, 'Grace Baptist Church, Ketu', 32, 'active');

-- Insert sample exams
INSERT INTO exams (title, description, duration, pass_mark, total_questions, status, created_by) VALUES
('Royal Ambassadors Fundamentals', 'Basic knowledge test for new Royal Ambassadors', 60, 70, 20, 'published', 3),
('Intermediate RA Knowledge', 'Test for advancing to higher ranks', 90, 75, 30, 'published', 3),
('Leadership Assessment', 'Assessment for leadership positions', 120, 80, 25, 'draft', 3);

-- Insert sample exam questions for first exam
INSERT INTO exam_questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option, points) VALUES
(1, 'What does OGBC stand for?', 'Ogun Gospel Baptist Convention', 'Ogun General Baptist Council', 'Ogun State Baptist Convention', 'Ogun Baptist Conference', 'C', 1),
(1, 'How many associations are under OGBC Royal Ambassadors?', '20', '23', '25', '30', 'C', 1),
(1, 'What is the highest rank in Royal Ambassadors?', 'Ambassador Extraordinary', 'Ambassador Plenipotentiary', 'Special Ambassador', 'Grand Ambassador', 'B', 1),
(1, 'What is the entry level rank for Royal Ambassadors?', 'Intern', 'Candidate', 'Assistant', 'Novice', 'B', 1),
(1, 'Who can approve exam results?', 'Association Presidents', 'Super Admins only', 'Any Ambassador', 'Both Presidents and Super Admins', 'B', 1);

-- Insert sample payments
INSERT INTO payments (user_id, type, amount, description, reference_number, status, verified_by) VALUES
(1, 'registration', 5000.00, 'Annual registration fee', 'REF001', 'approved', 3),
(2, 'exam', 2000.00, 'Exam registration fee', 'REF002', 'approved', 3),
(4, 'camp', 15000.00, 'Annual camp registration', 'REF003', 'pending', NULL),
(1, 'dues', 1000.00, 'Monthly dues payment', 'REF004', 'pending', NULL);

-- Insert sample notifications
INSERT INTO notifications (sender_id, title, message, type, recipient_type, recipient_id) VALUES
(3, 'Welcome to RA Portal', 'Welcome to the Royal Ambassadors Online Portal. Please complete your profile.', 'system', 'all', NULL),
(3, 'Exam Available', 'New exam "Royal Ambassadors Fundamentals" is now available for all candidates.', 'exam', 'ambassador', NULL),
(2, 'Association Meeting', 'Monthly association meeting scheduled for next Sunday.', 'general', 'association', 1);

-- Insert sample blog posts
INSERT INTO blogs (title, slug, content, excerpt, status, author_id) VALUES
('Welcome to Royal Ambassadors OGBC', 'welcome-to-royal-ambassadors-ogbc', 'This is the official launch of the Royal Ambassadors Online Portal for Ogun State Baptist Convention. Here you can manage your membership, take exams, make payments, and stay connected with your association.', 'Official launch of the Royal Ambassadors Online Portal', 'published', 3),
('Upcoming Annual Camp 2025', 'upcoming-annual-camp-2025', 'Get ready for the most exciting Royal Ambassadors camp of the year! Registration is now open for all associations.', 'Annual camp registration now open', 'published', 3);

-- Insert sample gallery items
INSERT INTO gallery (title, description, image_path, category, uploaded_by) VALUES
('2024 Annual Camp', 'Photos from the 2024 Royal Ambassadors annual camp', '/uploads/gallery/camp_2024_01.jpg', 'camp', 3),
('Leadership Training', 'Leadership training session for association presidents', '/images/Leadership.jpg', 'event', 3),
('Awards Ceremony', 'Annual awards ceremony for outstanding ambassadors', '/uploads/gallery/awards_2024.jpg', 'awards', 3);

-- Insert sample camp registration
INSERT INTO camp_registrations (association_id, camp_year, file_path, total_participants, status, uploaded_by) VALUES
(1, 2024, '/uploads/camp/agape_2024.xlsx', 25, 'approved', 2),
(2, 2024, '/uploads/camp/abeokuta_northwest_2024.xlsx', 18, 'pending', 5);