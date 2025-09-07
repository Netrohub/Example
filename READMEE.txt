 Overview

We are building an integrated Laravel + Discord bot system for managing game rallies and reinforcements.
The system will include:
	1.	A Laravel web dashboard with two main modules:
	•	Reinforcements Management
	•	Rally Timer Management
	2.	A Discord bot that executes actions in real-time inside Discord channels.
	3.	Shared MySQL database to keep both the bot and website fully synchronized.

The primary goal is to allow guild leaders and members to log in with Discord, manage all rallies and reinforcements from a clean, modern dashboard, and have the bot automatically send timed alerts to Discord.

⸻

Design Requirements
	•	Use the exact design and color scheme from the provided UI kit (rally-timer-pro-neon).
	•	Style: modern dark mode with neon accent colors.
	•	Two clear sections in the dashboard:
	1.	Reinforcements:
	•	Manage troop reinforcements and member assignments.
	•	Display which users are responsible for sending reinforcements.
	2.	Rally Timer:
	•	Create and track rallies, with countdowns and real-time updates.

⸻

Core Features

1. Discord Login (OAuth2)
	•	Users must sign in through Discord OAuth2.
	•	Required scopes: identify, guilds.
	•	Store basic user info:
	•	Discord ID
	•	Username + Tag
	•	Avatar
	•	Email (if provided)

⸻

2. Web Dashboard Modules

A) Reinforcements Section
	•	Assign specific members to reinforcement tasks.
	•	View who is responsible for each defense/attack.
	•	Optional alerts to notify members when it’s their turn.

B) Rally Timer Section
	•	Create a rally with the following fields:
	•	Rally name
	•	Rally preparation time (seconds or minutes)
	•	Attacker travel time
	•	Your troop travel time
	•	Safety buffer time
	•	Optional ping user or role (for @mention in Discord)
	•	Choose:
	•	Discord server
	•	Text channel
	•	See a list of active rallies and countdown timers.
	•	Cancel or edit existing rallies.

⸻

3. Discord Bot (Python + discord.py)
	•	Runs 24/7 on a VPS.
	•	Connects to the same MySQL database as Laravel OR polls Laravel’s API.
	•	When a new rally is created:
	•	The bot schedules two alerts:
	1.	Pre-alert: PREALERT_SECONDS before the calculated send time.
	2.	Main alert: Exactly at the calculated send time.
	•	Posts messages to the selected Discord channel with optional mentions.

Alert Flow Example:
⏳ @John 15 seconds be ready! To rein for rally #1 "Castle Defense".
attacker arrival: "countdown".
🚨 @John SEND REINFORCEMENTS NOW! (Rally #1 "Castle Defense")
 attacker arrival: "Countdown"
4. Timing Logic
The timing calculation ensures reinforcements arrive before the attacker:
Attacker Arrival = Now + Rally Preparation Time + Attacker Travel Time
Send Reinforcements Time = Attacker Arrival - (Your Travel Time + Safety Buffer)
5. Technical Architecture
	•	Backend: Laravel 11 (PHP 8.2+)
	•	Frontend: Blade + Bootstrap (or Tailwind if easier), based on provided design.
	•	Bot: Python 3.11 + discord.py
	•	Database: MySQL (shared between Laravel and bot)
	•	Hosting: VPS (Hostinger, DigitalOcean, etc.)

⸻

Integration Workflow
	1.	User logs in via Discord on the Laravel site.
	2.	User selects a Discord server and text channel.
	3.	Creates a new rally or reinforcement assignment.
	4.	Laravel saves the data into the shared MySQL database.
	5.	The bot continuously reads new entries:
	•	Schedules the timers.
	•	Sends alerts to the chosen Discord channel.

⸻

Example Rally Workflow
	1.	A guild leader logs into the dashboard.
	2.	Goes to the Rally Timer module.
	3.	Creates a rally:
	•	Rally Prep = 5 minutes
	•	Attacker Travel = 30 seconds
	•	User Travel = 20 seconds
	•	Safety Buffer = 3 seconds

4.	The bot calculates
Attacker Arrival = Now + 5:00 + 0:30
Send Time = Attacker Arrival - (0:20 + 0:03)
	5.	The bot sends:
	•	A pre-alert 15 seconds before send time.
	•	A main alert at the exact send time.

⸻

API Endpoints (Laravel → Bot)
	•	GET /api/rallies/pending → Bot fetches pending rallies.
	•	POST /api/rallies/{id}/ack → Bot confirms rally was scheduled.
	•	GET /api/discord/guilds → List guilds the bot is in.
	•	GET /api/discord/guilds/{id}/channels → List text channels in the guild.

⸻

Goals
	•	Deliver a fully integrated platform for managing game rallies and reinforcements.
	•	Provide a professional dashboard using the provided dark neon design.
	•	Ensure the bot and website are perfectly synchronized through a shared database.
	•	Make it simple for Discord users to log in, manage strategies, and receive real-time alerts.

⸻

Summary

The final product will allow guild leaders to:
	•	Log in with Discord to a beautifully designed dashboard.
	•	Organize reinforcements and rally timings from a central control panel.
	•	Have the bot automatically post alerts in the right Discord channel at the perfect timing.

This system will streamline guild coordination, reduce manual timing errors, and provide a seamless integration between Discord and a web-based management platform.