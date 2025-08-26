🌐 Mekelle University Research and Collaboration WordPress Project

📘 Overview
The Mekelle University Research and Collaboration WordPress Project is a web platform designed to showcase and manage research outputs, foster collaboration among faculty, students, and external partners, and enhance the visibility of Mekelle University’s academic contributions. Built on WordPress, this project includes custom themes and plugins to support research publication management, collaborative tools, and institutional repository integration. It aims to align with Mekelle University’s mission to advance knowledge and strengthen research partnerships.



✨ Features



Feature
Description



📚 Research Repository
Display and manage research papers, theses, and publications with metadata.


🤝 Collaboration Tools
Enable communication and project coordination among researchers and partners.


📊 Admin Dashboard
Administer content, user roles, and research submissions via a custom WordPress dashboard.


🔍 Search Functionality
Advanced search for research documents by author, date, or topic.


🌍 Internationalization
Support for multiple languages to engage global collaborators.


📬 Notifications
Email alerts for new research submissions or collaboration requests.


🎨 Custom Theme
Responsive, university-branded design for an enhanced user experience.






🛠️ Technologies Used

Platform: WordPress (CMS)
Backend: PHP (8.x), MySQL (database)
Frontend: HTML, CSS, JavaScript (with WordPress theme customization)
Plugins: Custom plugins for research management and collaboration (e.g., Advanced Custom Fields, WP User Frontend)
Tools: Git (version control), Composer (PHP dependencies), npm (frontend dependencies)


📂 Repository & File Structure
This project is hosted at https://github.com/Mikahaftamu/Mekelle_University_Research_Collaboration_WordPress as a private repository. The structure includes WordPress core files, custom themes, and plugins:
Mekelle_University_Research_Collaboration_WordPress/
├── wordpress/              # WordPress core files
│   ├── wp-admin/          # Admin interface
│   ├── wp-content/        # Themes, plugins, uploads
│   │   ├── themes/        # Custom theme (e.g., mu-research-theme/)
│   │   │   ├── style.css  # Theme stylesheet
│   │   │   ├── functions.php # Theme functions
│   │   │   └── ...
│   │   ├── plugins/       # Custom plugins (e.g., mu-research-plugin/)
│   │   │   ├── mu-research-plugin.php # Main plugin file
│   │   │   └── ...
│   │   └── uploads/       # Research document uploads
│   ├── wp-includes/       # WordPress core includes
│   └── wp-config.php      # Configuration file
├── .env                   # Environment variables (e.g., DB credentials)
├── docker-compose.yml     # Docker setup (optional)
├── README.md              # This file
└── .gitignore             # Excludes sensitive files (e.g., .env)

Note: The structure assumes a standard WordPress installation with custom additions. Share a specific directory listing if your setup differs.

🚀 Installation
Prerequisites

PHP >= 8.0
MySQL
Web server (e.g., Apache, Nginx)

Steps

Clone the Repository:
git clone https://github.com/Mikahaftamu/Mekelle_University_Research_Collaboration_WordPress.git
cd Mekelle_University_Research_Collaboration_WordPress


Set Up the Environment:

Copy .env.example to .env and configure database settings:cp .env.example .env


Update .env with MySQL credentials (e.g., DB_NAME, DB_USER, DB_PASSWORD).


Install Dependencies:

Install WordPress core (if not included):composer install


Install theme/plugin dependencies:cd wordpress/wp-content/themes/mu-research-theme
npm install




Configure WordPress:

Update wp-config.php with database details if needed.
Run the WordPress installation wizard at http://localhost/wp-admin/install.php (after starting the server).


Start the Server:

Using Docker (optional):docker-compose up -d


Or use a local server:php -S localhost:8000



Access at http://localhost:8000.

Build Assets:
cd wordpress/wp-content/themes/mu-research-theme
npm run build




🖥️ Usage

Researchers: Submit and manage research papers via the frontend or admin dashboard.
Administrators: Oversee content, approve submissions, and manage user roles.
Collaborators: Use integrated tools to coordinate projects and share resources.
