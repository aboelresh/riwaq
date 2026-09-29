# Riwaq - Database Schema

### 1. users
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | UNIQUE, NOT NULL |
| password | VARCHAR(255) | NOT NULL |
| role | ENUM('learner','admin') | DEFAULT 'learner' |
| email_verified_at | TIMESTAMP | NULLABLE |
| profile_photo | VARCHAR(255) | NULLABLE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 2. tracks
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| title | VARCHAR(255) | NOT NULL |
| description | TEXT | NULLABLE |
| created_by | BIGINT UNSIGNED | FOREIGN KEY (users.id) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 3. courses
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| title | VARCHAR(255) | NOT NULL |
| description | TEXT | NULLABLE |
| created_by | BIGINT UNSIGNED | FOREIGN KEY (users.id) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 4. track_courses (Pivot Table)
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| track_id | BIGINT UNSIGNED | FOREIGN KEY (tracks.id), ON DELETE CASCADE |
| course_id | BIGINT UNSIGNED | FOREIGN KEY (courses.id), ON DELETE CASCADE |
| order | INT | NOT NULL, DEFAULT 1 |

---

### 5. topics
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| title | VARCHAR(255) | NOT NULL |
| type | ENUM('article','video') | NOT NULL |
| content | TEXT | NULLABLE (for articles) |
| video_url | VARCHAR(255) | NULLABLE (for videos) |
| video_duration | INT | NULLABLE (seconds) |
| created_by | BIGINT UNSIGNED | FOREIGN KEY (users.id) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 6. course_topics (Pivot Table)
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| course_id | BIGINT UNSIGNED | FOREIGN KEY (courses.id), ON DELETE CASCADE |
| topic_id | BIGINT UNSIGNED | FOREIGN KEY (topics.id), ON DELETE CASCADE |
| order | INT | NOT NULL, DEFAULT 1 |

---

### 7. quizzes
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| title | VARCHAR(255) | NOT NULL |
| type | ENUM('assessment','topic','course') | NOT NULL |
| topic_id | BIGINT UNSIGNED | FOREIGN KEY (topics.id), NULLABLE, ON DELETE CASCADE |
| course_id | BIGINT UNSIGNED | FOREIGN KEY (courses.id), NULLABLE, ON DELETE CASCADE |
| total_points | INT | NOT NULL, DEFAULT 10 |
| pass_percentage | INT | NOT NULL, DEFAULT 50 |
| created_by | BIGINT UNSIGNED | FOREIGN KEY (users.id) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 8. questions
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| quiz_id | BIGINT UNSIGNED | FOREIGN KEY (quizzes.id), ON DELETE CASCADE |
| question_text | TEXT | NOT NULL |
| points | INT | NOT NULL, DEFAULT 1 |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 9. answers
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| question_id | BIGINT UNSIGNED | FOREIGN KEY (questions.id), ON DELETE CASCADE |
| answer_text | TEXT | NOT NULL |
| is_correct | BOOLEAN | NOT NULL, DEFAULT FALSE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 10. user_tracks
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FOREIGN KEY (users.id), ON DELETE CASCADE |
| track_id | BIGINT UNSIGNED | FOREIGN KEY (tracks.id), ON DELETE CASCADE |
| started_at | TIMESTAMP | |
| completed_at | TIMESTAMP | NULLABLE |

---

### 11. user_course_progress
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FOREIGN KEY (users.id), ON DELETE CASCADE |
| course_id | BIGINT UNSIGNED | FOREIGN KEY (courses.id), ON DELETE CASCADE |
| is_unlocked | BOOLEAN | DEFAULT FALSE |
| total_score | INT | DEFAULT 0 |
| max_possible_score | INT | DEFAULT 0 |
| is_completed | BOOLEAN | DEFAULT FALSE |
| started_at | TIMESTAMP | |
| completed_at | TIMESTAMP | NULLABLE |

---

### 12. user_topic_progress
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FOREIGN KEY (users.id), ON DELETE CASCADE |
| topic_id | BIGINT UNSIGNED | FOREIGN KEY (topics.id), ON DELETE CASCADE |
| is_unlocked | BOOLEAN | DEFAULT FALSE |
| is_viewed | BOOLEAN | DEFAULT FALSE |
| viewed_at | TIMESTAMP | NULLABLE |

---

### 13. user_quiz_attempts
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FOREIGN KEY (users.id), ON DELETE CASCADE |
| quiz_id | BIGINT UNSIGNED | FOREIGN KEY (quizzes.id), ON DELETE CASCADE |
| score | INT | NOT NULL |
| max_score | INT | NOT NULL |
| passed | BOOLEAN | NOT NULL |
| can_retry_at | TIMESTAMP | NULLABLE |
| attempted_at | TIMESTAMP | |

---

### 14. user_quiz_answers
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| attempt_id | BIGINT UNSIGNED | FOREIGN KEY (user_quiz_attempts.id), ON DELETE CASCADE |
| question_id | BIGINT UNSIGNED | FOREIGN KEY (questions.id), ON DELETE CASCADE |
| answer_id | BIGINT UNSIGNED | FOREIGN KEY (answers.id), ON DELETE CASCADE |
| is_correct | BOOLEAN | NOT NULL |
| created_at | TIMESTAMP | |

---

### 15. teams
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| code | VARCHAR(10) | UNIQUE, NOT NULL |
| project_type | ENUM('mobile','web') | NOT NULL |
| max_members | INT | NOT NULL |
| created_by | BIGINT UNSIGNED | FOREIGN KEY (users.id), ON DELETE CASCADE |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

### 16. team_members
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| team_id | BIGINT UNSIGNED | FOREIGN KEY (teams.id), ON DELETE CASCADE |
| user_id | BIGINT UNSIGNED | FOREIGN KEY (users.id), ON DELETE CASCADE |
| track_id | BIGINT UNSIGNED | FOREIGN KEY (tracks.id), ON DELETE CASCADE |
| joined_at | TIMESTAMP | |

---

### 17. videos
| Column | Type | Attributes |
|--------|------|------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| title | VARCHAR(255) | NOT NULL |
| filename | VARCHAR(255) | NOT NULL |
| path | VARCHAR(255) | NOT NULL |
| size | BIGINT | NOT NULL (bytes) |
| duration | INT | NULLABLE (seconds) |
| uploaded_by | BIGINT UNSIGNED | FOREIGN KEY (users.id) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |