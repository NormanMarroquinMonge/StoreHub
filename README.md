<h1 align="center">StoreHub: Full-Stack Inventory & POS System</h1>

<p align="center">
  <strong>An online retail platform.</strong>
</p>

<hr />

<h2>🚀 Project Overview</h2>
<p>
  StoreHub is an integrated e-commerce and Point of Sale (POS) solution developed as a collaborative project for <b>Database Systems I</b> at <b>Frostburg State University</b>. The platform bridges the gap between front-end retail and back-end management, providing tools for customer engagement and administrative oversight[cite: 8, 11].
</p>



<h2>🛠️ Tech Stack</h2>
<ul>
  <li><b>Backend:</b> PHP (Modular logic and session management)</li>
  <li><b>Database:</b> MySQL / MariaDB (Relational schema with 14+ tables)</li>
  <li><b>Frontend:</b> HTML5, CSS3, JavaScript / jQuery (AJAX-driven updates)</li>
  <li><b>Server:</b> LAMP Stack environment</li>
</ul>



<h2>📂 Key Functionalities</h2>

<h3>For Customers</h3>
<ul>
  <li><b>Categorized Browsing:</b> Organized product navigation across Beverages, Snacks, Produce, Dairy, and Bakery.</li>
  <li><b>Secure Account Management:</b> User registration and login utilizing hashed passwords for security.</li>
  <li><b>Integrated Shopping Cart:</b> Persistent cart states allowing users to manage items before a streamlined checkout.</li>
  <li><b>In-Store Credit:</b> A dedicated system for managing and applying store credit to purchases.</li>
</ul>

<h3>For Employees & Admin</h3>
<ul>
  <li><b>Inventory Control:</b> Complete CRUD functionality to track stock levels, restock dates, and product details.</li>
  <li><b>Transaction Processing:</b> Automated and manual checkout flows that update sales records and inventory in real-time.</li>
  <li><b>Return Management:</b> A specialized module to log returns, restock items, and automate customer refunds.</li>
  <li><b>Role-Based Access:</b> Dynamic redirection based on user roles (Manager vs. Cashier).</li>
</ul>

<h2>📊 Database Architecture</h2>
<p>
  The system is built on a complex relational model designed for data integrity and scalability.
</p>
<ul>
  <li><b>Transactional Integrity:</b> Employs foreign key constraints and PDO prepared statements to protect against SQL injection.</li>
  <li><b>Sales Tracking:</b> Real-time logging of revenue and item volume through <code>daily_sales</code> and <code>order_history</code> tables.</li>
  <li><b>Promotional Logic:</b> Dynamic discount application during checkout based on active product or category promotions.</li>
</ul>



[Image of Relational Diagram]


<hr />
