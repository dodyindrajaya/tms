<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTmsV1Schema extends Migration
{
    public function up()
    {
        $db = $this->db;

        // ---------- SYSTEM / CORE ----------
        $this->createTable('roles', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'name VARCHAR(100) NOT NULL UNIQUE',
            'description VARCHAR(255) NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ]);

        $this->createTable('permissions', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'code VARCHAR(100) NOT NULL UNIQUE',
            'name VARCHAR(150) NOT NULL',
            'description VARCHAR(255) NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ]);

        $this->createTable('users', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'username VARCHAR(100) NOT NULL UNIQUE',
            'email VARCHAR(190) NOT NULL UNIQUE',
            'password_hash VARCHAR(255) NOT NULL',
            'role_id BIGINT UNSIGNED NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'last_login_at DATETIME NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_users_role (role_id)'
        ], [
            'FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('role_permissions', [
            'role_id BIGINT UNSIGNED NOT NULL',
            'permission_id BIGINT UNSIGNED NOT NULL',
            'PRIMARY KEY (role_id, permission_id)'
        ], [
            'FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $this->createTable('customers', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'customer_code VARCHAR(30) NOT NULL UNIQUE',
            'name VARCHAR(190) NOT NULL',
            'customer_type VARCHAR(20) NOT NULL DEFAULT "individual"',
            'phone VARCHAR(30) NULL',
            'email VARCHAR(190) NULL',
            'address TEXT NULL',
            'city VARCHAR(100) NULL',
            'country_code CHAR(2) NULL',
            'tax_id VARCHAR(50) NULL',
            'notes TEXT NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_customers_phone (phone)'
        ]);

        $this->createTable('passengers', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'customer_id BIGINT UNSIGNED NULL',
            'passenger_code VARCHAR(30) NOT NULL UNIQUE',
            'full_name VARCHAR(190) NOT NULL',
            'gender VARCHAR(1) NULL',
            'birth_date DATE NULL',
            'nationality_code CHAR(2) NULL',
            'passport_no VARCHAR(50) NULL',
            'passport_expiry DATE NULL',
            'id_number VARCHAR(50) NULL',
            'phone VARCHAR(30) NULL',
            'email VARCHAR(190) NULL',
            'notes TEXT NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_passengers_customer (customer_id)',
            'INDEX idx_passengers_passport (passport_no)'
        ], [
            'FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('suppliers', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'supplier_code VARCHAR(30) NOT NULL UNIQUE',
            'name VARCHAR(190) NOT NULL',
            'supplier_type VARCHAR(30) NOT NULL',
            'phone VARCHAR(30) NULL',
            'email VARCHAR(190) NULL',
            'address TEXT NULL',
            'payment_terms_days INT NOT NULL DEFAULT 0',
            'notes TEXT NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ]);

        $this->createTable('products', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'product_code VARCHAR(50) NOT NULL UNIQUE',
            'name VARCHAR(190) NOT NULL',
            'category VARCHAR(30) NOT NULL',
            'unit VARCHAR(30) NOT NULL DEFAULT "unit"',
            'default_sale_price DECIMAL(18,2) NOT NULL DEFAULT 0',
            'default_cost_price DECIMAL(18,2) NOT NULL DEFAULT 0',
            'revenue_account_id BIGINT UNSIGNED NULL',
            'cost_account_id BIGINT UNSIGNED NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ]);

        // ---------- ACCOUNTING ----------
        $this->createTable('account_groups', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'code VARCHAR(30) NOT NULL UNIQUE',
            'name VARCHAR(190) NOT NULL',
            'report_type VARCHAR(30) NOT NULL',
            'parent_id BIGINT UNSIGNED NULL',
            'sort_order INT NOT NULL DEFAULT 0',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_account_groups_parent (parent_id)'
        ], [
            'FOREIGN KEY (parent_id) REFERENCES account_groups(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('accounts', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'code VARCHAR(20) NOT NULL UNIQUE',
            'name VARCHAR(190) NOT NULL',
            'account_type VARCHAR(20) NOT NULL',
            'parent_id BIGINT UNSIGNED NULL',
            'account_group_id BIGINT UNSIGNED NULL',
            'is_control_account TINYINT(1) NOT NULL DEFAULT 0',
            'allow_manual_posting TINYINT(1) NOT NULL DEFAULT 1',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_accounts_parent (parent_id)',
            'INDEX idx_accounts_group (account_group_id)'
        ], [
            'FOREIGN KEY (parent_id) REFERENCES accounts(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (account_group_id) REFERENCES account_groups(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('fiscal_years', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'year INT NOT NULL UNIQUE',
            'start_date DATE NOT NULL',
            'end_date DATE NOT NULL',
            'status VARCHAR(20) NOT NULL DEFAULT "open"',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ]);

        $this->createTable('fiscal_periods', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'fiscal_year_id BIGINT UNSIGNED NOT NULL',
            'period_no INT NOT NULL',
            'start_date DATE NOT NULL',
            'end_date DATE NOT NULL',
            'status VARCHAR(20) NOT NULL DEFAULT "open"',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'UNIQUE KEY uq_fiscal_period (fiscal_year_id, period_no)'
        ], [
            'FOREIGN KEY (fiscal_year_id) REFERENCES fiscal_years(id) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $this->createTable('journals', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'code VARCHAR(20) NOT NULL UNIQUE',
            'name VARCHAR(100) NOT NULL',
            'journal_type VARCHAR(20) NOT NULL',
            'default_debit_account_id BIGINT UNSIGNED NULL',
            'default_credit_account_id BIGINT UNSIGNED NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (default_debit_account_id) REFERENCES accounts(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (default_credit_account_id) REFERENCES accounts(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('payment_methods', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'code VARCHAR(30) NOT NULL UNIQUE',
            'name VARCHAR(100) NOT NULL',
            'method_type VARCHAR(30) NOT NULL',
            'clearing_account_id BIGINT UNSIGNED NOT NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (clearing_account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);

        $this->createTable('taxes', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'code VARCHAR(30) NOT NULL UNIQUE',
            'name VARCHAR(100) NOT NULL',
            'rate DECIMAL(8,4) NOT NULL DEFAULT 0',
            'tax_type VARCHAR(30) NOT NULL',
            'tax_account_id BIGINT UNSIGNED NOT NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (tax_account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);

        $this->createTable('journal_entries', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'journal_id BIGINT UNSIGNED NOT NULL',
            'entry_no VARCHAR(40) NOT NULL UNIQUE',
            'entry_date DATE NOT NULL',
            'reference_type VARCHAR(50) NULL',
            'reference_id BIGINT UNSIGNED NULL',
            'description VARCHAR(255) NULL',
            'status VARCHAR(20) NOT NULL DEFAULT "draft"',
            'posted_at DATETIME NULL',
            'posted_by BIGINT UNSIGNED NULL',
            'created_by BIGINT UNSIGNED NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_je_date (entry_date)',
            'INDEX idx_je_reference (reference_type, reference_id)'
        ], [
            'FOREIGN KEY (journal_id) REFERENCES journals(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('journal_entry_lines', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'journal_entry_id BIGINT UNSIGNED NOT NULL',
            'account_id BIGINT UNSIGNED NOT NULL',
            'customer_id BIGINT UNSIGNED NULL',
            'supplier_id BIGINT UNSIGNED NULL',
            'booking_id BIGINT UNSIGNED NULL',
            'description VARCHAR(255) NULL',
            'debit DECIMAL(18,2) NOT NULL DEFAULT 0',
            'credit DECIMAL(18,2) NOT NULL DEFAULT 0',
            'line_date DATE NOT NULL',
            'INDEX idx_jel_account_date (account_id, line_date)',
            'INDEX idx_jel_customer (customer_id)',
            'INDEX idx_jel_supplier (supplier_id)',
            'INDEX idx_jel_booking (booking_id)'
        ], [
            'FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        // ---------- TOUR ----------
        $this->createTable('tour_packages', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'product_id BIGINT UNSIGNED NOT NULL',
            'package_code VARCHAR(50) NOT NULL UNIQUE',
            'name VARCHAR(190) NOT NULL',
            'destination VARCHAR(255) NULL',
            'duration_days INT NOT NULL DEFAULT 1',
            'duration_nights INT NOT NULL DEFAULT 0',
            'description TEXT NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);

        $this->createTable('tour_departures', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'tour_package_id BIGINT UNSIGNED NOT NULL',
            'departure_date DATE NOT NULL',
            'return_date DATE NULL',
            'capacity INT NOT NULL DEFAULT 0',
            'status VARCHAR(20) NOT NULL DEFAULT "draft"',
            'meeting_point VARCHAR(255) NULL',
            'notes TEXT NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_departure_date (departure_date)'
        ], [
            'FOREIGN KEY (tour_package_id) REFERENCES tour_packages(id) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $this->createTable('tour_itineraries', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'tour_package_id BIGINT UNSIGNED NOT NULL',
            'day_no INT NOT NULL',
            'title VARCHAR(190) NOT NULL',
            'description TEXT NULL',
            'location VARCHAR(190) NULL',
            'meal VARCHAR(100) NULL',
            'hotel VARCHAR(190) NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'UNIQUE KEY uq_itinerary_day (tour_package_id, day_no)'
        ], [
            'FOREIGN KEY (tour_package_id) REFERENCES tour_packages(id) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $this->createTable('tour_components', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'tour_package_id BIGINT UNSIGNED NOT NULL',
            'product_id BIGINT UNSIGNED NOT NULL',
            'supplier_id BIGINT UNSIGNED NULL',
            'component_type VARCHAR(30) NOT NULL',
            'quantity DECIMAL(12,2) NOT NULL DEFAULT 1',
            'estimated_cost DECIMAL(18,2) NOT NULL DEFAULT 0',
            'notes TEXT NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (tour_package_id) REFERENCES tour_packages(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        // ---------- BOOKING / TICKETING ----------
        $this->createTable('bookings', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'booking_no VARCHAR(40) NOT NULL UNIQUE',
            'customer_id BIGINT UNSIGNED NOT NULL',
            'booking_date DATETIME NOT NULL',
            'travel_start_date DATE NULL',
            'travel_end_date DATE NULL',
            'source VARCHAR(20) NOT NULL DEFAULT "walk_in"',
            'status VARCHAR(30) NOT NULL DEFAULT "draft"',
            'currency_code CHAR(3) NOT NULL DEFAULT "IDR"',
            'subtotal DECIMAL(18,2) NOT NULL DEFAULT 0',
            'discount_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'tax_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'total_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'paid_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'outstanding_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'notes TEXT NULL',
            'created_by BIGINT UNSIGNED NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_booking_customer_date (customer_id, booking_date)',
            'INDEX idx_booking_status (status)'
        ], [
            'FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('booking_passengers', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'booking_id BIGINT UNSIGNED NOT NULL',
            'passenger_id BIGINT UNSIGNED NOT NULL',
            'passenger_type VARCHAR(20) NOT NULL DEFAULT "adult"',
            'is_primary TINYINT(1) NOT NULL DEFAULT 0',
            'UNIQUE KEY uq_booking_passenger (booking_id, passenger_id)'
        ], [
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (passenger_id) REFERENCES passengers(id) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);

        $this->createTable('booking_items', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'booking_id BIGINT UNSIGNED NOT NULL',
            'product_id BIGINT UNSIGNED NOT NULL',
            'description VARCHAR(255) NOT NULL',
            'quantity DECIMAL(12,2) NOT NULL DEFAULT 1',
            'unit_sale_price DECIMAL(18,2) NOT NULL DEFAULT 0',
            'discount_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'tax_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'line_total DECIMAL(18,2) NOT NULL DEFAULT 0',
            'revenue_account_id BIGINT UNSIGNED NULL',
            'cost_account_id BIGINT UNSIGNED NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (revenue_account_id) REFERENCES accounts(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (cost_account_id) REFERENCES accounts(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('ticket_bookings', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'booking_id BIGINT UNSIGNED NOT NULL',
            'passenger_id BIGINT UNSIGNED NOT NULL',
            'ticket_type VARCHAR(20) NOT NULL',
            'supplier_id BIGINT UNSIGNED NULL',
            'booking_code VARCHAR(50) NULL',
            'ticket_number VARCHAR(100) NULL',
            'issue_date DATE NULL',
            'departure_date DATE NULL',
            'departure_time TIME NULL',
            'arrival_date DATE NULL',
            'arrival_time TIME NULL',
            'origin VARCHAR(100) NULL',
            'destination VARCHAR(100) NULL',
            'carrier VARCHAR(190) NULL',
            'travel_class VARCHAR(50) NULL',
            'seat VARCHAR(30) NULL',
            'status VARCHAR(30) NOT NULL DEFAULT "request"',
            'cost_price DECIMAL(18,2) NOT NULL DEFAULT 0',
            'selling_price DECIMAL(18,2) NOT NULL DEFAULT 0',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_ticket_booking (booking_id)',
            'INDEX idx_ticket_number (ticket_number)'
        ], [
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (passenger_id) REFERENCES passengers(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('ticket_segments', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'ticket_booking_id BIGINT UNSIGNED NOT NULL',
            'segment_no INT NOT NULL',
            'origin VARCHAR(100) NOT NULL',
            'destination VARCHAR(100) NOT NULL',
            'carrier VARCHAR(190) NULL',
            'service_no VARCHAR(50) NULL',
            'departure_date DATE NULL',
            'departure_time TIME NULL',
            'arrival_date DATE NULL',
            'arrival_time TIME NULL',
            'travel_class VARCHAR(50) NULL',
            'seat VARCHAR(30) NULL',
            'UNIQUE KEY uq_ticket_segment (ticket_booking_id, segment_no)'
        ], [
            'FOREIGN KEY (ticket_booking_id) REFERENCES ticket_bookings(id) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $this->createTable('payment_schedules', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'booking_id BIGINT UNSIGNED NOT NULL',
            'sequence_no INT NOT NULL',
            'due_date DATE NOT NULL',
            'description VARCHAR(190) NULL',
            'amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'paid_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'status VARCHAR(20) NOT NULL DEFAULT "pending"',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'UNIQUE KEY uq_payment_schedule (booking_id, sequence_no)'
        ], [
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        // ---------- FINANCE ----------
        $this->createTable('booking_costs', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'booking_id BIGINT UNSIGNED NOT NULL',
            'booking_item_id BIGINT UNSIGNED NULL',
            'supplier_id BIGINT UNSIGNED NOT NULL',
            'description VARCHAR(255) NOT NULL',
            'cost_type VARCHAR(30) NOT NULL',
            'amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'bill_id BIGINT UNSIGNED NULL',
            'status VARCHAR(20) NOT NULL DEFAULT "estimated"',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (booking_item_id) REFERENCES booking_items(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);

        $this->createTable('invoices', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'invoice_no VARCHAR(40) NOT NULL UNIQUE',
            'booking_id BIGINT UNSIGNED NOT NULL',
            'customer_id BIGINT UNSIGNED NOT NULL',
            'invoice_date DATE NOT NULL',
            'due_date DATE NULL',
            'status VARCHAR(20) NOT NULL DEFAULT "draft"',
            'subtotal DECIMAL(18,2) NOT NULL DEFAULT 0',
            'tax_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'total_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'paid_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'outstanding_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'journal_entry_id BIGINT UNSIGNED NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('invoice_items', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'invoice_id BIGINT UNSIGNED NOT NULL',
            'booking_item_id BIGINT UNSIGNED NULL',
            'description VARCHAR(255) NOT NULL',
            'quantity DECIMAL(12,2) NOT NULL DEFAULT 1',
            'unit_price DECIMAL(18,2) NOT NULL DEFAULT 0',
            'tax_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'line_total DECIMAL(18,2) NOT NULL DEFAULT 0',
            'revenue_account_id BIGINT UNSIGNED NOT NULL'
        ], [
            'FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (booking_item_id) REFERENCES booking_items(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (revenue_account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);

        $this->createTable('supplier_bills', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'bill_no VARCHAR(40) NOT NULL UNIQUE',
            'supplier_id BIGINT UNSIGNED NOT NULL',
            'booking_id BIGINT UNSIGNED NULL',
            'bill_date DATE NOT NULL',
            'due_date DATE NULL',
            'status VARCHAR(20) NOT NULL DEFAULT "draft"',
            'total_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'paid_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'outstanding_amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'journal_entry_id BIGINT UNSIGNED NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('supplier_bill_items', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'supplier_bill_id BIGINT UNSIGNED NOT NULL',
            'booking_cost_id BIGINT UNSIGNED NULL',
            'description VARCHAR(255) NOT NULL',
            'quantity DECIMAL(12,2) NOT NULL DEFAULT 1',
            'unit_cost DECIMAL(18,2) NOT NULL DEFAULT 0',
            'line_total DECIMAL(18,2) NOT NULL DEFAULT 0',
            'expense_account_id BIGINT UNSIGNED NOT NULL'
        ], [
            'FOREIGN KEY (supplier_bill_id) REFERENCES supplier_bills(id) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (booking_cost_id) REFERENCES booking_costs(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (expense_account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE'
        ]);

        $this->createTable('payments', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'payment_no VARCHAR(40) NOT NULL UNIQUE',
            'payment_date DATETIME NOT NULL',
            'payment_type VARCHAR(30) NOT NULL',
            'booking_id BIGINT UNSIGNED NULL',
            'customer_id BIGINT UNSIGNED NULL',
            'supplier_id BIGINT UNSIGNED NULL',
            'account_id BIGINT UNSIGNED NOT NULL',
            'amount DECIMAL(18,2) NOT NULL DEFAULT 0',
            'payment_method_id BIGINT UNSIGNED NOT NULL',
            'reference_no VARCHAR(100) NULL',
            'notes TEXT NULL',
            'journal_entry_id BIGINT UNSIGNED NULL',
            'created_by BIGINT UNSIGNED NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ], [
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        // ---------- WHATSAPP ----------
        $this->createTable('whatsapp_configs', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'name VARCHAR(100) NOT NULL',
            'phone_number_id VARCHAR(100) NOT NULL',
            'business_account_id VARCHAR(100) NOT NULL',
            'api_base_url VARCHAR(255) NOT NULL',
            'encrypted_access_token TEXT NOT NULL',
            'webhook_verify_token VARCHAR(255) NOT NULL',
            'is_active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ]);

        $this->createTable('whatsapp_templates', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'name VARCHAR(100) NOT NULL UNIQUE',
            'provider_template_name VARCHAR(190) NOT NULL',
            'language VARCHAR(20) NOT NULL DEFAULT "id"',
            'event_code VARCHAR(80) NOT NULL',
            'body TEXT NOT NULL',
            'active TINYINT(1) NOT NULL DEFAULT 1',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL'
        ]);

        $this->createTable('whatsapp_messages', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'config_id BIGINT UNSIGNED NOT NULL',
            'customer_id BIGINT UNSIGNED NULL',
            'booking_id BIGINT UNSIGNED NULL',
            'template_id BIGINT UNSIGNED NULL',
            'direction VARCHAR(10) NOT NULL',
            'phone_number VARCHAR(30) NOT NULL',
            'message_text TEXT NULL',
            'media_url VARCHAR(500) NULL',
            'provider_message_id VARCHAR(190) NULL',
            'status VARCHAR(20) NOT NULL',
            'error_message TEXT NULL',
            'sent_at DATETIME NULL',
            'delivered_at DATETIME NULL',
            'read_at DATETIME NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_wa_provider_id (provider_message_id)',
            'INDEX idx_wa_customer_booking (customer_id, booking_id)'
        ], [
            'FOREIGN KEY (config_id) REFERENCES whatsapp_configs(id) ON DELETE RESTRICT ON UPDATE CASCADE',
            'FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL ON UPDATE CASCADE',
            'FOREIGN KEY (template_id) REFERENCES whatsapp_templates(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('whatsapp_webhooks', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'provider_event_id VARCHAR(190) NULL',
            'payload_json JSON NOT NULL',
            'event_type VARCHAR(100) NULL',
            'processed TINYINT(1) NOT NULL DEFAULT 0',
            'processed_at DATETIME NULL',
            'error_message TEXT NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_wa_webhook_processed (processed)'
        ]);

        // ---------- SYSTEM ----------
        $this->createTable('attachments', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'entity_type VARCHAR(50) NOT NULL',
            'entity_id BIGINT UNSIGNED NOT NULL',
            'file_name VARCHAR(255) NOT NULL',
            'file_path VARCHAR(500) NOT NULL',
            'mime_type VARCHAR(100) NULL',
            'file_size BIGINT UNSIGNED NULL',
            'uploaded_by BIGINT UNSIGNED NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_attachment_entity (entity_type, entity_id)'
        ], [
            'FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        $this->createTable('audit_logs', [
            'id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
            'user_id BIGINT UNSIGNED NULL',
            'action VARCHAR(50) NOT NULL',
            'entity_type VARCHAR(50) NOT NULL',
            'entity_id BIGINT UNSIGNED NOT NULL',
            'old_values JSON NULL',
            'new_values JSON NULL',
            'ip_address VARCHAR(45) NULL',
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
            'INDEX idx_audit_entity (entity_type, entity_id)',
            'INDEX idx_audit_user (user_id)'
        ], [
            'FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);

        // Circular references that are cleaner to add after all dependent tables exist.
        $db->query("ALTER TABLE products
            ADD CONSTRAINT fk_products_revenue FOREIGN KEY (revenue_account_id) REFERENCES accounts(id)
            ON DELETE SET NULL ON UPDATE CASCADE,
            ADD CONSTRAINT fk_products_cost FOREIGN KEY (cost_account_id) REFERENCES accounts(id)
            ON DELETE SET NULL ON UPDATE CASCADE");

        $db->query("ALTER TABLE journal_entry_lines
            ADD CONSTRAINT fk_jel_booking FOREIGN KEY (booking_id) REFERENCES bookings(id)
            ON DELETE SET NULL ON UPDATE CASCADE");

        $db->query("ALTER TABLE booking_costs
            ADD CONSTRAINT fk_booking_cost_bill FOREIGN KEY (bill_id) REFERENCES supplier_bills(id)
            ON DELETE SET NULL ON UPDATE CASCADE");
    }

    private function createTable(string $name, array $columns, array $foreignKeys = []): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$name}` (" . implode(",\n", $columns);
        if ($foreignKeys) {
            $sql .= ",\n" . implode(",\n", $foreignKeys);
        }
        $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
        $this->db->query($sql);
    }

    public function down()
    {
        $tables = [
            'audit_logs','attachments','whatsapp_webhooks','whatsapp_messages','whatsapp_templates','whatsapp_configs',
            'payments','supplier_bill_items','supplier_bills','invoice_items','invoices','booking_costs',
            'payment_schedules','ticket_segments','ticket_bookings','booking_items','booking_passengers','bookings',
            'tour_components','tour_itineraries','tour_departures','tour_packages',
            'journal_entry_lines','journal_entries','taxes','payment_methods','journals','fiscal_periods','fiscal_years',
            'accounts','account_groups',
            'products','suppliers','passengers','customers','role_permissions','users','permissions','roles'
        ];
        foreach ($tables as $table) {
            $this->db->query("DROP TABLE IF EXISTS `{$table}`");
        }
    }
}
