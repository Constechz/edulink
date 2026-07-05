<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Library Tables
        Schema::create('library_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('library_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('published_year')->nullable();
            $table->integer('copies_total')->default(1);
            $table->integer('copies_available')->default(1);
            $table->string('location_rack')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // student or staff
            $table->date('loan_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
            $table->timestamps();
        });

        Schema::create('library_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('reservation_date');
            $table->enum('status', ['pending', 'fulfilled', 'expired'])->default('pending');
            $table->timestamps();
        });

        Schema::create('library_fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('loan_id')->constrained('library_loans')->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->date('paid_date')->nullable();
            $table->timestamps();
        });

        // Inventory Tables
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('inventory_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('description')->nullable();
            $table->string('unit_of_measure')->default('pcs'); // pcs, box, litres, pack
            $table->integer('quantity_in_stock')->default(0);
            $table->integer('reorder_level')->default(5);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('order_number');
            $table->date('order_date');
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'approved', 'received', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->unique(['school_id', 'order_number']);
        });

        Schema::create('po_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('quantity');
            $table->string('reference_type')->nullable(); // Purchase Order, Requisition, Sale
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->date('transaction_date');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->enum('status', ['active', 'maintenance', 'disposed'])->default('active');
            $table->timestamps();
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->date('assigned_date');
            $table->date('returned_date')->nullable();
            $table->timestamps();
        });

        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->enum('status', ['pending', 'approved', 'rejected', 'issued'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Hostel Tables
        Schema::create('dormitories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');
            $table->enum('gender_allowed', ['Male', 'Female', 'CoEd']);
            $table->foreignId('warden_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('capacity');
            $table->timestamps();
        });

        Schema::create('dormitory_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dormitory_id')->constrained('dormitories')->cascadeOnDelete();
            $table->string('room_number');
            $table->integer('capacity');
            $table->timestamps();
        });

        Schema::create('dormitory_beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('dormitory_rooms')->cascadeOnDelete();
            $table->string('bed_number');
            $table->boolean('is_occupied')->default(false);
            $table->timestamps();
        });

        Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('bed_id')->constrained('dormitory_beds')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->date('allocated_date');
            $table->date('vacated_date')->nullable();
            $table->timestamps();
        });

        Schema::create('hostel_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('dormitory_id')->constrained('dormitories')->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->date('due_date');
            $table->timestamps();
        });

        Schema::create('hostel_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->date('incident_date');
            $table->text('description');
            $table->text('action_taken')->nullable();
            $table->timestamps();
        });

        Schema::create('hostel_visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('visitor_name');
            $table->string('visitor_phone');
            $table->date('visit_date');
            $table->time('check_in_time');
            $table->time('check_out_time')->nullable();
            $table->timestamps();
        });

        Schema::create('hostel_rollcalls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('dormitory_id')->constrained('dormitories')->cascadeOnDelete();
            $table->date('rollcall_date');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Transport Tables
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('plate_number');
            $table->string('model');
            $table->integer('capacity');
            $table->date('insurance_expiry')->nullable();
            $table->date('roadworthy_expiry')->nullable();
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('route_name');
            $table->string('start_point');
            $table->string('end_point')->nullable();
            $table->timestamps();
        });

        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('transport_routes')->cascadeOnDelete();
            $table->string('stop_name');
            $table->time('pickup_time')->nullable();
            $table->time('dropoff_time')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('license_number');
            $table->date('license_expiry');
            $table->timestamps();
        });

        Schema::create('vehicle_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('transport_routes')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('transport_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('transport_routes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->timestamps();
        });

        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->date('refuel_date');
            $table->decimal('quantity_litres', 8, 2);
            $table->decimal('cost_per_litre', 8, 2);
            $table->decimal('total_cost', 10, 2);
            $table->integer('odometer_reading')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->date('service_date');
            $table->text('description');
            $table->decimal('cost', 10, 2);
            $table->string('vendor_name')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('fuel_logs');
        Schema::dropIfExists('transport_fees');
        Schema::dropIfExists('vehicle_allocations');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('hostel_rollcalls');
        Schema::dropIfExists('hostel_visitors');
        Schema::dropIfExists('hostel_incidents');
        Schema::dropIfExists('hostel_fees');
        Schema::dropIfExists('hostel_allocations');
        Schema::dropIfExists('dormitory_beds');
        Schema::dropIfExists('dormitory_rooms');
        Schema::dropIfExists('dormitories');
        Schema::dropIfExists('requisitions');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('po_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
        Schema::dropIfExists('library_fines');
        Schema::dropIfExists('library_reservations');
        Schema::dropIfExists('library_loans');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('library_categories');
    }
};
