# 🚗 Caranology — Car Q&A Community

A full-stack web application where car enthusiasts can ask questions, share answers, and vote on the best solutions.

## 🌐 Live Demo
http://www.webdev.cs.uregina.ca/~dtw348/Project2-Caranology/login.php

## 💡 What It Does
Caranology is a community platform where users can create an account, post car-related questions, answer other people's questions, and upvote or downvote answers based on how helpful they are.

## 🛠️ How I Built It
I built this as a full-stack web application using:
- HTML, CSS, JavaScript for the frontend
- PHP for the backend and server-side logic
- MySQL for the database to store users, questions, answers and votes
- Sessions for user authentication and login persistence

I designed the entire database schema from scratch including 4 tables — users, questions, answers, and votes — with foreign key relationships between them.

## ✨ Features
- Full user authentication — signup, login, logout with password hashing
- Ask car questions with a title and detailed description
- Answer any question posted by the community
- Upvote and downvote questions — prevents double voting
- Manage your own questions — edit and delete them
- Live search to filter questions instantly
- Profile photo upload on signup
- Fully responsive — desktop website layout and mobile app layout with bottom navigation
- Input validation with clear error messages

## 🗄️ Tech Stack
- Frontend: HTML5, CSS3, JavaScript
- Backend: PHP
- Database: MySQL
- Server: University of Regina CS Webdev Server
