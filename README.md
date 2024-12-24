# coddycamp-lms

LMS System for CoddyCamp IT Academy
This Learning Management System (LMS) was developed for CoddyCamp IT Academy, serving over 2,000 students. It is designed to manage courses, students, and communication efficiently.

Main Technologies

Frontend: HTML, CSS, JavaScript, jQuery

Backend: PHP, MySQL

Tools: AJAX, WebSocket, Cron Jobs

Project Structure
Main modules are located in the account/modules/med/ directory.

Elements and modals called via AJAX are located in the account/ajax/ directory.

Key Modules and Features

Student Courses Module (student_courses):
Manages courses, modules, and lessons for the online course platform.

Banner Section Module (banner_section):
Allows administrators to manage course content through the admin panel.

Course Students Module (course_students):
Provides a system to attach and manage students enrolled in programming courses.

Student Chat Module (student_chat):
Features a chat system for students and groups, enabling them to ask questions and interact via chat.

Student Homework Module (student_homework):
Manages students' tasks and assignments on the platform.

Telegram Messaging Module (tg_rassilka):
Implements a system for sending messages to groups or general readers via a Telegram bot. Operations are automated using a cron job.
