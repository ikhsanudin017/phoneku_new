# PhoneKu Microservice Application

A modern e-commerce platform for smartphones and accessories built with microservice architecture using Laravel API backend and Vue.js frontend.

## 🏗️ Architecture

This application follows a microservice architecture pattern:

- **Backend API**: Laravel 11 with Sanctum authentication
- **Frontend SPA**: Vue.js 3 with Vite, Pinia state management
- **Database**: MySQL with Eloquent ORM
- **Authentication**: JWT tokens via Laravel Sanctum
- **Styling**: Tailwind CSS with responsive design

## 🚀 Features

### Customer Features
- **Authentication**: Register, login, logout with role-based access
- **Product Catalog**: Browse smartphones and accessories with search/filter
- **Shopping Cart**: Add, update, remove items with real-time updates
- **User Profile**: Manage personal information, email/phone with OTP verification
- **Customer Support**: Real-time chat with admin support
- **Responsive Design**: Mobile-first approach with Tailwind CSS

### Admin Features
- **Admin Dashboard**: Statistics and overview of the platform
- **Product Management**: CRUD operations for products with image uploads
- **User Management**: Manage customer accounts and information
- **Chat Management**: Respond to customer inquiries in real-time
- **Role-based Access**: Secure admin-only sections

### Technical Features
- **API-First Design**: RESTful API with proper HTTP status codes
- **Real-time Updates**: Auto-refresh for cart count and chat messages
- **Error Handling**: Comprehensive error handling with user-friendly messages
- **Security**: CORS configuration, input validation, and authentication middleware
- **State Management**: Centralized state with Pinia stores
- **Routing**: Vue Router with navigation guards for authentication

## 📁 Project Structure

```
phoneku-microservice/
├── backend/                      # Laravel Backend API
│   ├── app/
│   │   ├── Http/Controllers/Api/ # API Controllers
│   │   ├── Models/              # Eloquent Models
│   │   └── Http/Middleware/     # Custom Middleware
│   ├── routes/api.php           # API Routes
│   ├── config/cors.php          # CORS Configuration
│   ├── database/seeders/        # Database Seeders
│   └── bootstrap/app.php        # Application Bootstrap
│
├── frontend/                    # Vue.js Frontend
│   ├── src/
│   │   ├── views/              # Vue Components/Pages
│   │   ├── stores/             # Pinia Stores
│   │   ├── services/           # API Services
│   │   ├── router/             # Vue Router Configuration
│   │   └── components/         # Reusable Components
│   ├── tailwind.config.js      # Tailwind Configuration
│   └── vite.config.js          # Vite Configuration
│
├── README.md                   # Project Documentation
├── API_DOCUMENTATION.md        # API Documentation
└── *.bat                      # Windows batch scripts
```

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js 16 or higher
- npm or yarn
- MySQL database

### Quick Start (Recommended)

1. **Complete Setup (First Time)**
   ```bash
   setup-project.bat
   ```
   This will automatically:
   - Install all backend dependencies (Composer)
   - Install all frontend dependencies (npm)
   - Set up environment files
   - Run database migrations and seeders
   - Create storage links

2. **Start Development Servers**
   ```bash
   start-servers.bat
   ```
   This will start both backend and frontend servers simultaneously.

3. **Access Application**
   - Frontend: http://localhost:5173
   - Backend API: http://localhost:8000

4. **Default Login Credentials**
   - Admin: `admin@phoneku.com` / `admin123`
   - Customer: `customer@phoneku.com` / `customer123`

### Manual Setup (Advanced)

#### Backend Setup (Laravel API)

1. **Navigate to backend directory**
   ```bash
   cd backend
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=phoneku_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run database migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Create storage link**
   ```bash
   php artisan storage:link
   ```

7. **Start Laravel server**
   ```bash
   php artisan serve
   ```

#### Frontend Setup (Vue.js)

1. **Navigate to frontend directory**
   ```bash
   cd frontend
   ```

2. **Install Node.js dependencies**
   ```bash
   npm install
   ```

3. **Start development server**
   ```bash
   npm run dev
   ```

## 🔧 Available Scripts

### Management Scripts
- `setup-project.bat` - Complete setup (first time)
- `start-servers.bat` - Start both servers
- `stop-servers.bat` - Stop all servers
- `setup-database.bat` - Database setup only
- `run-tests.bat` - Run all tests
- `test-api.bat` - Test API endpoints

### Backend Scripts (Laravel)
- `php artisan serve` - Start Laravel development server
- `php artisan migrate:fresh --seed` - Reset database with sample data
- `php artisan test` - Run PHPUnit tests
- `php artisan storage:link` - Create storage symbolic link

### Frontend Scripts (Vue.js)
- `npm run dev` - Start Vite development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build
- `npm run lint` - Run ESLint
- `npm run test:e2e` - Run Playwright E2E tests

## 🔧 API Endpoints

### Authentication
- `POST /api/auth/register` - Customer registration
- `POST /api/auth/login` - Customer login
- `POST /api/auth/admin/login` - Admin login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/user` - Get authenticated user

### Products
- `GET /api/products` - Get all products
- `GET /api/products/featured` - Get featured products
- `GET /api/products/search` - Search products
- `GET /api/products/{id}` - Get product details

### Cart (Protected)
- `GET /api/cart` - Get cart items
- `POST /api/cart/add/{productId}` - Add item to cart
- `PUT /api/cart/{id}` - Update cart item quantity
- `DELETE /api/cart/{id}` - Remove cart item
- `GET /api/cart/count` - Get cart count

### User Profile (Protected)
- `GET /api/user/profile` - Get user profile
- `PUT /api/user/profile` - Update profile
- `POST /api/user/email/request-otp` - Request email change OTP
- `POST /api/user/email/verify-otp` - Verify email change

### Chat (Protected)
- `GET /api/chat/customer` - Get customer chat messages
- `POST /api/chat/send` - Send message

### Admin (Admin Only)
- `GET /api/admin/dashboard` - Dashboard statistics
- `GET /api/admin/users` - Manage users
- `POST /api/admin/products` - Create product
- `PUT /api/admin/products/{id}` - Update product
- `DELETE /api/admin/products/{id}` - Delete product

## 🎨 Frontend Routes

### Public Routes
- `/` - Homepage redirect to welcome
- `/welcome` - Landing page with featured products
- `/products` - Product catalog
- `/product/:id` - Product detail page
- `/login` - Customer login
- `/register` - Customer registration
- `/about` - About us page
- `/kontak` - Contact page

### Protected Routes (Customer)
- `/cart` - Shopping cart
- `/checkout` - Checkout process
- `/profile` - User profile management
- `/chat` - Customer support chat

### Admin Routes
- `/admin/login` - Admin login
- `/admin/dashboard` - Admin dashboard
- `/admin/products` - Product management
- `/admin/users` - User management
- `/admin/chat` - Chat management

## 🔐 Authentication & Authorization

The application uses Laravel Sanctum for API authentication:

- **Token-based Authentication**: JWT tokens stored in localStorage
- **Role-based Access Control**: Customer and Admin roles
- **Route Protection**: Navigation guards in Vue Router
- **Automatic Token Refresh**: Interceptors handle token validation
- **Secure Logout**: Tokens are revoked on logout

## 🎯 State Management

### Pinia Stores

1. **Auth Store** (`frontend/src/stores/auth.js`)
   - User authentication state
   - Login/logout functionality
   - Role-based access control

2. **Cart Store** (`frontend/src/stores/cart.js`)
   - Shopping cart management
   - Real-time cart count updates
   - Cart operations (add, update, remove)

## 🌐 API Service Layer

The frontend uses a centralized API service (`frontend/src/services/api.js`) with:
- Axios interceptors for token management
- Automatic error handling
- Base URL configuration
- Request/response transformations

## 🎨 UI/UX Features

- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Modern Interface**: Clean and intuitive user interface
- **Loading States**: Skeleton loaders and spinners
- **Error Handling**: User-friendly error messages
- **Real-time Updates**: Live cart count and chat messages
- **Accessibility**: Proper ARIA labels and keyboard navigation

## 🚀 Deployment

### Backend Deployment
1. Configure production environment variables
2. Set up database and run migrations
3. Configure web server (Apache/Nginx)
4. Set up SSL certificates
5. Configure file storage and permissions

### Frontend Deployment
1. Build production assets: `npm run build`
2. Deploy to static hosting (Netlify, Vercel, etc.)
3. Configure environment variables for production API
4. Set up CDN for optimal performance

## 🔧 Troubleshooting

### Common Issues

1. **Port already in use**
   - Stop existing servers: `stop-servers.bat`
   - Check running processes and kill if necessary

2. **Database connection errors**
   - Verify database credentials in `backend/.env`
   - Ensure MySQL server is running
   - Run `setup-database.bat` to reset database

3. **npm/composer dependency issues**
   - Delete `node_modules` folder and run `npm install`
   - Delete `vendor` folder and run `composer install`

4. **File permission errors**
   - Run `php artisan storage:link` in backend folder
   - Check folder permissions for storage directories

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the API documentation: `API_DOCUMENTATION.md`

## 🔄 Version History

- **v1.0.0** - Initial microservice implementation
  - Laravel API backend with Sanctum authentication
  - Vue.js frontend with Pinia state management
  - Complete e-commerce functionality
  - Real-time chat system
  - Admin dashboard and management
  - Organized folder structure

---

**Built with ❤️ using Laravel and Vue.js** 