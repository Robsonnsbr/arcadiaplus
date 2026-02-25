CREATE TABLE "blackboard_agent_logs" (
	"id" serial PRIMARY KEY NOT NULL,
	"task_id" integer,
	"agent_name" varchar(100) NOT NULL,
	"action" varchar(100) NOT NULL,
	"thought" text,
	"observation" text,
	"metadata" jsonb,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "blackboard_artifacts" (
	"id" serial PRIMARY KEY NOT NULL,
	"task_id" integer NOT NULL,
	"type" varchar(50) NOT NULL,
	"name" varchar(255) NOT NULL,
	"content" text,
	"metadata" jsonb,
	"version" integer DEFAULT 1,
	"created_by" varchar(100),
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "blackboard_tasks" (
	"id" serial PRIMARY KEY NOT NULL,
	"parent_id" integer,
	"type" varchar(50) NOT NULL,
	"title" varchar(500) NOT NULL,
	"description" text,
	"status" varchar(30) DEFAULT 'pending',
	"priority" integer DEFAULT 5,
	"assigned_agent" varchar(100),
	"dependencies" jsonb,
	"context" jsonb,
	"result" jsonb,
	"error_message" text,
	"user_id" varchar(255),
	"started_at" timestamp,
	"completed_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_channels" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"type" varchar(30) NOT NULL,
	"name" varchar(200) NOT NULL,
	"identifier" varchar(200),
	"status" varchar(30) DEFAULT 'disconnected',
	"config" jsonb,
	"greeting_message" text,
	"out_of_hours_message" text,
	"schedules" jsonb,
	"source_ref" varchar(50),
	"is_active" boolean DEFAULT true,
	"last_connected_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_contacts" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"type" varchar(30) DEFAULT 'lead',
	"name" varchar(200) NOT NULL,
	"email" varchar(200),
	"phone" varchar(50),
	"whatsapp" varchar(50),
	"avatar_url" text,
	"company" varchar(200),
	"trade_name" varchar(200),
	"cnpj" varchar(20),
	"position" varchar(100),
	"website" varchar(300),
	"address" text,
	"city" varchar(100),
	"state" varchar(50),
	"country" varchar(50) DEFAULT 'Brasil',
	"segment" varchar(100),
	"tags" text[],
	"custom_fields" jsonb,
	"lead_score" integer DEFAULT 0,
	"lead_status" varchar(30) DEFAULT 'new',
	"source" varchar(50),
	"source_details" text,
	"assigned_to" varchar,
	"primary_contact_name" varchar(200),
	"primary_contact_email" varchar(200),
	"primary_contact_phone" varchar(50),
	"notes" text,
	"last_contact_at" timestamp,
	"converted_at" timestamp,
	"xos_contact_id" integer,
	"crm_client_id" integer,
	"crm_lead_id" integer,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_events" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"type" varchar(50) NOT NULL,
	"entity_type" varchar(30) NOT NULL,
	"entity_id" integer NOT NULL,
	"data" jsonb,
	"processed_by_kg" boolean DEFAULT false,
	"processed_by_agents" boolean DEFAULT false,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_messages" (
	"id" serial PRIMARY KEY NOT NULL,
	"thread_id" integer NOT NULL,
	"channel_id" integer,
	"direction" varchar(10) NOT NULL,
	"sender_type" varchar(20) NOT NULL,
	"sender_id" varchar,
	"sender_name" varchar(200),
	"content" text,
	"content_type" varchar(30) DEFAULT 'text',
	"media_url" text,
	"media_type" varchar(30),
	"attachments" jsonb,
	"metadata" jsonb,
	"external_id" varchar(200),
	"status" varchar(20) DEFAULT 'sent',
	"is_from_agent" boolean DEFAULT false,
	"read_at" timestamp,
	"delivered_at" timestamp,
	"xos_message_id" integer,
	"crm_message_id" integer,
	"whatsapp_message_id" integer,
	"email_message_id" integer,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_queue_members" (
	"id" serial PRIMARY KEY NOT NULL,
	"queue_id" integer NOT NULL,
	"user_id" varchar NOT NULL,
	"role" varchar(20) DEFAULT 'agent',
	"is_available" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_queues" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"color" varchar(20) DEFAULT 'blue',
	"greeting_message" text,
	"out_of_hours_message" text,
	"schedules" jsonb,
	"auto_assign" boolean DEFAULT false,
	"assignment_method" varchar(20) DEFAULT 'round_robin',
	"order_priority" integer DEFAULT 0,
	"is_active" boolean DEFAULT true,
	"xos_queue_id" integer,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_quick_messages" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"shortcode" varchar(50) NOT NULL,
	"title" varchar(200),
	"content" text NOT NULL,
	"media_url" text,
	"media_type" varchar(30),
	"category" varchar(50),
	"scope" varchar(20) DEFAULT 'company',
	"user_id" varchar,
	"variables" text[],
	"usage_count" integer DEFAULT 0,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "comm_threads" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"contact_id" integer,
	"channel_id" integer,
	"channel" varchar(30) NOT NULL,
	"external_id" varchar(200),
	"status" varchar(20) DEFAULT 'open',
	"priority" varchar(20) DEFAULT 'normal',
	"subject" varchar(300),
	"assigned_to" varchar,
	"queue_id" integer,
	"tags" text[],
	"metadata" jsonb,
	"messages_count" integer DEFAULT 0,
	"unread_count" integer DEFAULT 0,
	"first_response_at" timestamp,
	"last_message_at" timestamp,
	"resolved_at" timestamp,
	"satisfaction_score" integer,
	"satisfaction_comment" text,
	"xos_conversation_id" integer,
	"crm_thread_id" integer,
	"whatsapp_ticket_id" integer,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "customer_credits" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"store_id" integer,
	"person_id" integer NOT NULL,
	"customer_name" varchar(200) NOT NULL,
	"customer_cpf" varchar(20),
	"amount" numeric(12, 2) NOT NULL,
	"used_amount" numeric(12, 2) DEFAULT '0',
	"remaining_amount" numeric(12, 2) NOT NULL,
	"origin" varchar(50) NOT NULL,
	"origin_id" integer,
	"description" text,
	"expires_at" timestamp,
	"status" varchar(20) DEFAULT 'active',
	"used_in_sale_id" integer,
	"created_by" varchar,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "etl_migration_jobs" (
	"id" serial PRIMARY KEY NOT NULL,
	"user_id" varchar,
	"staged_table_id" integer,
	"mapping_id" integer,
	"erp_connection_id" integer,
	"status" text DEFAULT 'pending',
	"total_records" integer DEFAULT 0,
	"processed_records" integer DEFAULT 0,
	"success_records" integer DEFAULT 0,
	"error_records" integer DEFAULT 0,
	"error_log" text,
	"started_at" timestamp,
	"completed_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "marketplace_modules" (
	"id" serial PRIMARY KEY NOT NULL,
	"code" varchar(50) NOT NULL,
	"name" varchar(100) NOT NULL,
	"description" text,
	"long_description" text,
	"category" varchar(50) NOT NULL,
	"icon" varchar(100),
	"color" varchar(20),
	"image_url" text,
	"price" numeric(12, 2) DEFAULT '0',
	"setup_fee" numeric(12, 2) DEFAULT '0',
	"features" text[],
	"dependencies" text[],
	"route" varchar(100),
	"api_endpoint" varchar(200),
	"version" varchar(20) DEFAULT '1.0.0',
	"is_core" boolean DEFAULT false,
	"is_active" boolean DEFAULT true,
	"is_featured" boolean DEFAULT false,
	"sort_order" integer DEFAULT 0,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	CONSTRAINT "marketplace_modules_code_unique" UNIQUE("code")
);
--> statement-breakpoint
CREATE TABLE "migration_logs" (
	"id" serial PRIMARY KEY NOT NULL,
	"job_id" integer NOT NULL,
	"mapping_id" integer,
	"level" varchar(20) DEFAULT 'info',
	"message" text NOT NULL,
	"source_id" varchar(100),
	"target_id" varchar(100),
	"details" jsonb,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "migration_mappings" (
	"id" serial PRIMARY KEY NOT NULL,
	"job_id" integer NOT NULL,
	"source_entity" varchar(100) NOT NULL,
	"target_entity" varchar(100) NOT NULL,
	"field_mappings" jsonb NOT NULL,
	"transformations" jsonb,
	"filters" jsonb,
	"is_enabled" boolean DEFAULT true,
	"priority" integer DEFAULT 0,
	"record_count" integer DEFAULT 0,
	"imported_count" integer DEFAULT 0,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "migration_templates" (
	"id" serial PRIMARY KEY NOT NULL,
	"name" varchar(200) NOT NULL,
	"description" text,
	"source_type" varchar(50) NOT NULL,
	"source_system" varchar(100),
	"mappings" jsonb NOT NULL,
	"is_public" boolean DEFAULT false,
	"usage_count" integer DEFAULT 0,
	"created_by" varchar(255),
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "module_subscriptions" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer NOT NULL,
	"module_id" integer NOT NULL,
	"status" varchar(20) DEFAULT 'active',
	"start_date" date NOT NULL,
	"end_date" date,
	"trial_ends_at" timestamp,
	"price" numeric(12, 2),
	"discount" numeric(5, 2) DEFAULT '0',
	"billing_cycle" varchar(20) DEFAULT 'monthly',
	"auto_renew" boolean DEFAULT true,
	"notes" text,
	"activated_by" varchar,
	"activated_at" timestamp,
	"cancelled_by" varchar,
	"cancelled_at" timestamp,
	"cancellation_reason" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "module_usage" (
	"id" serial PRIMARY KEY NOT NULL,
	"subscription_id" integer NOT NULL,
	"tenant_id" integer NOT NULL,
	"module_code" varchar(50) NOT NULL,
	"period" varchar(7) NOT NULL,
	"usage_count" integer DEFAULT 0,
	"api_calls" integer DEFAULT 0,
	"storage_used_mb" numeric(12, 2) DEFAULT '0',
	"documents_generated" integer DEFAULT 0,
	"active_users" integer DEFAULT 0,
	"metadata" jsonb,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "pos_cash_movements" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"session_id" integer NOT NULL,
	"store_id" integer NOT NULL,
	"type" varchar(20) NOT NULL,
	"amount" numeric(12, 2) NOT NULL,
	"reason" text,
	"performed_by" varchar,
	"performed_by_name" varchar(200),
	"authorized_by" varchar,
	"authorized_by_name" varchar(200),
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_acquisitions" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"acquisition_number" varchar(20) NOT NULL,
	"type" varchar(30) NOT NULL,
	"source_evaluation_id" integer,
	"source_service_order_id" integer,
	"device_id" integer,
	"imei" varchar(20),
	"brand" varchar(50),
	"model" varchar(100),
	"condition" varchar(30),
	"acquisition_cost" numeric(12, 2) DEFAULT '0',
	"repair_cost" numeric(12, 2) DEFAULT '0',
	"total_cost" numeric(12, 2) DEFAULT '0',
	"suggested_price" numeric(12, 2),
	"final_price" numeric(12, 2),
	"linked_product_id" integer,
	"linked_device_id" integer,
	"status" varchar(30) DEFAULT 'pending',
	"notes" text,
	"processed_by" varchar,
	"processed_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_activity_feed" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"store_id" integer,
	"activity_type" varchar(50) NOT NULL,
	"entity_type" varchar(50),
	"entity_id" integer,
	"title" varchar(200) NOT NULL,
	"description" text,
	"metadata" jsonb,
	"severity" varchar(20) DEFAULT 'info',
	"created_by" varchar,
	"created_by_name" varchar(200),
	"is_read" boolean DEFAULT false,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_commission_closure_items" (
	"id" serial PRIMARY KEY NOT NULL,
	"closure_id" integer NOT NULL,
	"sale_id" integer,
	"return_id" integer,
	"item_type" varchar(20) NOT NULL,
	"amount" numeric(12, 2) NOT NULL,
	"commission" numeric(12, 2) DEFAULT '0',
	"original_date" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_commission_closures" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"store_id" integer,
	"seller_id" integer,
	"period_type" varchar(20) NOT NULL,
	"period_start" date NOT NULL,
	"period_end" date NOT NULL,
	"total_sales" numeric(14, 2) DEFAULT '0',
	"total_returns" numeric(14, 2) DEFAULT '0',
	"net_sales" numeric(14, 2) DEFAULT '0',
	"commission_rate" numeric(5, 2) DEFAULT '0',
	"commission_amount" numeric(12, 2) DEFAULT '0',
	"bonus_amount" numeric(12, 2) DEFAULT '0',
	"total_amount" numeric(12, 2) DEFAULT '0',
	"sales_count" integer DEFAULT 0,
	"returns_count" integer DEFAULT 0,
	"status" varchar(20) DEFAULT 'open',
	"closed_at" timestamp,
	"closed_by" integer,
	"paid_at" timestamp,
	"notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_commission_plans" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"description" text,
	"type" varchar(30) NOT NULL,
	"base_value" numeric(12, 2) DEFAULT '0',
	"base_percent" numeric(5, 2) DEFAULT '0',
	"rules" jsonb,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_inventories" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"warehouse_id" integer NOT NULL,
	"inventory_number" varchar(20) NOT NULL,
	"type" varchar(30) DEFAULT 'full',
	"status" varchar(30) DEFAULT 'open',
	"started_at" timestamp,
	"completed_at" timestamp,
	"notes" text,
	"created_by" varchar,
	"completed_by" varchar,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_inventory_items" (
	"id" serial PRIMARY KEY NOT NULL,
	"inventory_id" integer NOT NULL,
	"product_id" integer NOT NULL,
	"system_quantity" numeric(12, 4),
	"counted_quantity" numeric(12, 4),
	"difference" numeric(12, 4),
	"adjustment_applied" boolean DEFAULT false,
	"counted_by" varchar,
	"counted_at" timestamp,
	"notes" text
);
--> statement-breakpoint
CREATE TABLE "retail_payment_methods" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"type" varchar(30) NOT NULL,
	"brand" varchar(50),
	"fee_percent" numeric(5, 2) DEFAULT '0',
	"fixed_fee" numeric(12, 2) DEFAULT '0',
	"installments_max" integer DEFAULT 1,
	"installment_fees" jsonb,
	"days_to_receive" integer DEFAULT 1,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_price_table_items" (
	"id" serial PRIMARY KEY NOT NULL,
	"price_table_id" integer NOT NULL,
	"product_id" integer,
	"device_id" integer,
	"product_code" varchar(50),
	"custom_price" numeric(12, 2),
	"discount_percent" numeric(5, 2),
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_price_tables" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"code" varchar(20),
	"description" text,
	"customer_type" varchar(50),
	"discount_percent" numeric(5, 2) DEFAULT '0',
	"markup_percent" numeric(5, 2) DEFAULT '0',
	"valid_from" date,
	"valid_to" date,
	"is_default" boolean DEFAULT false,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_product_serials" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"product_id" integer NOT NULL,
	"warehouse_id" integer,
	"serial_number" varchar(50),
	"imei" varchar(20),
	"imei2" varchar(20),
	"status" varchar(30) DEFAULT 'in_stock',
	"acquisition_cost" numeric(12, 4),
	"sale_price" numeric(12, 4),
	"sold_price" numeric(12, 4),
	"movement_id" integer,
	"sale_movement_id" integer,
	"purchase_nfe_number" varchar(50),
	"sale_nfe_number" varchar(50),
	"customer_id" integer,
	"notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_product_types" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"description" text,
	"category" varchar(30) NOT NULL,
	"requires_imei" boolean DEFAULT false,
	"requires_serial" boolean DEFAULT false,
	"ncm" varchar(10),
	"cest" varchar(9),
	"origem" integer DEFAULT 0,
	"cst_icms" varchar(3),
	"csosn" varchar(3),
	"cfop_venda_estadual" varchar(4) DEFAULT '5102',
	"cfop_venda_interestadual" varchar(4) DEFAULT '6102',
	"cfop_devolucao_estadual" varchar(4) DEFAULT '1202',
	"cfop_devolucao_interestadual" varchar(4) DEFAULT '2202',
	"aliq_icms" numeric(5, 2),
	"aliq_pis" numeric(5, 4) DEFAULT '0.65',
	"aliq_cofins" numeric(5, 4) DEFAULT '3.00',
	"aliq_ipi" numeric(5, 2) DEFAULT '0',
	"class_trib_ibs" varchar(20),
	"aliq_ibs" numeric(5, 2),
	"class_trib_cbs" varchar(20),
	"aliq_cbs" numeric(5, 2),
	"tax_group_id" integer,
	"unidade_medida" varchar(6) DEFAULT 'UN',
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_promotions" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"description" text,
	"type" varchar(30) NOT NULL,
	"discount_value" numeric(12, 2),
	"discount_percent" numeric(5, 2),
	"apply_to" varchar(30) DEFAULT 'all',
	"apply_to_ids" jsonb,
	"price_table_id" integer,
	"min_quantity" integer DEFAULT 1,
	"max_quantity" integer,
	"valid_from" timestamp,
	"valid_to" timestamp,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_reports" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"description" text,
	"type" varchar(30) DEFAULT 'custom',
	"query" text,
	"filters" jsonb,
	"columns" jsonb,
	"is_system" boolean DEFAULT false,
	"is_active" boolean DEFAULT true,
	"created_by" varchar,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_seller_goals" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"seller_id" integer,
	"store_id" integer,
	"month" integer NOT NULL,
	"year" integer NOT NULL,
	"goal_amount" numeric(14, 2) NOT NULL,
	"goal_type" varchar(20) DEFAULT 'sales',
	"achieved_amount" numeric(14, 2) DEFAULT '0',
	"achieved_percent" numeric(5, 2) DEFAULT '0',
	"bonus" numeric(12, 2) DEFAULT '0',
	"notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_sellers" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"person_id" integer,
	"code" varchar(20),
	"name" varchar(200) NOT NULL,
	"email" varchar(100),
	"phone" varchar(20),
	"store_id" integer,
	"commission_plan_id" integer,
	"hire_date" date,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_stock_movements" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"warehouse_id" integer NOT NULL,
	"product_id" integer NOT NULL,
	"movement_type" varchar(30) NOT NULL,
	"operation_type" varchar(50),
	"quantity" numeric(12, 4) NOT NULL,
	"previous_stock" numeric(12, 4),
	"new_stock" numeric(12, 4),
	"unit_cost" numeric(12, 4),
	"total_cost" numeric(12, 4),
	"reference_type" varchar(50),
	"reference_id" integer,
	"reference_number" varchar(50),
	"supplier_id" integer,
	"notes" text,
	"user_id" varchar,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_stock_transfer_items" (
	"id" serial PRIMARY KEY NOT NULL,
	"transfer_id" integer NOT NULL,
	"product_id" integer NOT NULL,
	"requested_quantity" numeric(12, 4) NOT NULL,
	"transferred_quantity" numeric(12, 4),
	"received_quantity" numeric(12, 4),
	"notes" text
);
--> statement-breakpoint
CREATE TABLE "retail_stock_transfers" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"transfer_number" varchar(20) NOT NULL,
	"source_warehouse_id" integer NOT NULL,
	"destination_warehouse_id" integer NOT NULL,
	"status" varchar(30) DEFAULT 'pending',
	"notes" text,
	"requested_by" varchar,
	"approved_by" varchar,
	"completed_by" varchar,
	"requested_at" timestamp DEFAULT CURRENT_TIMESTAMP,
	"approved_at" timestamp,
	"completed_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_store_goals" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"store_id" integer,
	"month" integer NOT NULL,
	"year" integer NOT NULL,
	"goal_amount" numeric(14, 2) NOT NULL,
	"achieved_amount" numeric(14, 2) DEFAULT '0',
	"achieved_percent" numeric(5, 2) DEFAULT '0',
	"notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_transfer_serials" (
	"id" serial PRIMARY KEY NOT NULL,
	"transfer_item_id" integer NOT NULL,
	"serial_id" integer NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_warehouse_stock" (
	"id" serial PRIMARY KEY NOT NULL,
	"warehouse_id" integer NOT NULL,
	"product_id" integer NOT NULL,
	"quantity" numeric(12, 4) DEFAULT '0' NOT NULL,
	"reserved_quantity" numeric(12, 4) DEFAULT '0',
	"available_quantity" numeric(12, 4) DEFAULT '0',
	"min_stock" numeric(12, 4),
	"max_stock" numeric(12, 4),
	"last_movement_at" timestamp,
	"last_inventory_at" timestamp,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "service_warranties" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"store_id" integer,
	"service_order_id" integer NOT NULL,
	"device_id" integer,
	"imei" varchar(20),
	"service_type" varchar(50) NOT NULL,
	"warranty_days" integer NOT NULL,
	"start_date" date NOT NULL,
	"end_date" date NOT NULL,
	"customer_name" varchar(200),
	"customer_phone" varchar(20),
	"description" text,
	"status" varchar(20) DEFAULT 'active',
	"claimed_at" timestamp,
	"claim_notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "tenant_empresas" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer NOT NULL,
	"razao_social" text NOT NULL,
	"nome_fantasia" text,
	"cnpj" text NOT NULL,
	"ie" text,
	"im" text,
	"email" text,
	"phone" text,
	"tipo" text DEFAULT 'filial',
	"status" text DEFAULT 'active',
	"cep" text,
	"logradouro" text,
	"numero" text,
	"complemento" text,
	"bairro" text,
	"cidade" text,
	"uf" text,
	"codigo_ibge" text,
	"regime_tributario" text,
	"certificado_digital_id" integer,
	"ambiente_fiscal" text DEFAULT 'homologacao',
	"serie_nfe" integer DEFAULT 1,
	"serie_nfce" integer DEFAULT 1,
	"plus_empresa_id" integer,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_activities" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"type" varchar(30) NOT NULL,
	"title" varchar(300) NOT NULL,
	"description" text,
	"status" varchar(20) DEFAULT 'pending',
	"priority" varchar(20) DEFAULT 'normal',
	"due_at" timestamp,
	"completed_at" timestamp,
	"contact_id" integer,
	"company_id" integer,
	"deal_id" integer,
	"assigned_to" varchar,
	"created_by" varchar,
	"outcome" text,
	"duration" integer,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_agent_metrics" (
	"id" serial PRIMARY KEY NOT NULL,
	"agent_name" text NOT NULL,
	"period" text NOT NULL,
	"tasks_completed" integer DEFAULT 0,
	"tasks_failed" integer DEFAULT 0,
	"avg_duration_ms" integer,
	"skills_created" integer DEFAULT 0,
	"policies_triggered" integer DEFAULT 0,
	"metadata" jsonb,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP
);
--> statement-breakpoint
CREATE TABLE "xos_audit_trail" (
	"id" serial PRIMARY KEY NOT NULL,
	"correlation_id" text,
	"agent_name" varchar(100) NOT NULL,
	"action" varchar(255) NOT NULL,
	"target" varchar(500),
	"decision" varchar(30) NOT NULL,
	"justification" text,
	"input" jsonb,
	"output" jsonb,
	"task_id" integer,
	"policy_id" integer,
	"metadata" jsonb,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_automations" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(200) NOT NULL,
	"description" text,
	"trigger_type" varchar(50) NOT NULL,
	"trigger_config" jsonb,
	"actions" jsonb,
	"conditions" jsonb,
	"is_active" boolean DEFAULT true,
	"execution_count" integer DEFAULT 0,
	"last_executed_at" timestamp,
	"created_by" varchar,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_campaigns" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(200) NOT NULL,
	"description" text,
	"type" varchar(30) NOT NULL,
	"status" varchar(20) DEFAULT 'draft',
	"segment_query" jsonb,
	"template_id" integer,
	"content" text,
	"subject" varchar(300),
	"scheduled_at" timestamp,
	"started_at" timestamp,
	"completed_at" timestamp,
	"stats" jsonb,
	"created_by" varchar,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_companies" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(200) NOT NULL,
	"trade_name" varchar(200),
	"document" varchar(20),
	"domain" varchar(200),
	"industry" varchar(100),
	"size" varchar(30),
	"employees" integer,
	"annual_revenue" numeric(18, 2),
	"phone" varchar(50),
	"email" varchar(200),
	"website" varchar(300),
	"address" text,
	"city" varchar(100),
	"state" varchar(50),
	"country" varchar(50) DEFAULT 'Brasil',
	"logo_url" text,
	"tags" text[],
	"custom_fields" jsonb,
	"assigned_to" varchar,
	"notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_contacts" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"type" varchar(20) DEFAULT 'lead',
	"name" varchar(200) NOT NULL,
	"email" varchar(200),
	"phone" varchar(50),
	"whatsapp" varchar(50),
	"avatar_url" text,
	"company" varchar(200),
	"position" varchar(100),
	"tags" text[],
	"custom_fields" jsonb,
	"lead_score" integer DEFAULT 0,
	"lead_status" varchar(30) DEFAULT 'new',
	"source" varchar(50),
	"source_details" text,
	"assigned_to" varchar,
	"last_contact_at" timestamp,
	"address" text,
	"city" varchar(100),
	"state" varchar(50),
	"country" varchar(50) DEFAULT 'Brasil',
	"notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_contract_registry" (
	"id" serial PRIMARY KEY NOT NULL,
	"name" varchar(255) NOT NULL,
	"action" varchar(255) NOT NULL,
	"description" text,
	"input_schema" jsonb,
	"output_schema" jsonb,
	"required_permissions" text[],
	"category" varchar(100),
	"is_active" boolean DEFAULT true NOT NULL,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	CONSTRAINT "xos_contract_registry_name_unique" UNIQUE("name")
);
--> statement-breakpoint
CREATE TABLE "xos_conversation_tracking" (
	"id" serial PRIMARY KEY NOT NULL,
	"conversation_id" integer NOT NULL,
	"queue_id" integer,
	"queued_at" timestamp,
	"first_response_at" timestamp,
	"assigned_at" timestamp,
	"chatbot_ended_at" timestamp,
	"human_started_at" timestamp,
	"resolved_at" timestamp,
	"rated_at" timestamp,
	"rating_score" integer,
	"rating_comment" text,
	"total_duration_seconds" integer,
	"response_time_seconds" integer
);
--> statement-breakpoint
CREATE TABLE "xos_conversations" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"contact_id" integer,
	"channel" varchar(30) NOT NULL,
	"channel_id" varchar(100),
	"status" varchar(20) DEFAULT 'open',
	"priority" varchar(20) DEFAULT 'normal',
	"subject" varchar(300),
	"assigned_to" varchar,
	"tags" text[],
	"metadata" jsonb,
	"first_response_at" timestamp,
	"resolved_at" timestamp,
	"satisfaction_score" integer,
	"satisfaction_comment" text,
	"messages_count" integer DEFAULT 0,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_deals" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"pipeline_id" integer NOT NULL,
	"stage_id" integer NOT NULL,
	"contact_id" integer,
	"company_id" integer,
	"title" varchar(200) NOT NULL,
	"value" numeric(18, 2) DEFAULT '0',
	"currency" varchar(3) DEFAULT 'BRL',
	"expected_close_date" date,
	"actual_close_date" date,
	"probability" integer DEFAULT 0,
	"status" varchar(20) DEFAULT 'open',
	"lost_reason" text,
	"won_reason" text,
	"assigned_to" varchar,
	"tags" text[],
	"custom_fields" jsonb,
	"notes" text,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"closed_at" timestamp
);
--> statement-breakpoint
CREATE TABLE "xos_dev_pipelines" (
	"id" serial PRIMARY KEY NOT NULL,
	"correlation_id" text DEFAULT gen_random_uuid() NOT NULL,
	"prompt" text NOT NULL,
	"status" text DEFAULT 'queued' NOT NULL,
	"current_phase" text DEFAULT 'queued',
	"main_task_id" integer,
	"user_id" text DEFAULT 'system',
	"phases" jsonb,
	"metadata" jsonb,
	"error" text,
	"budget" jsonb,
	"runbook" jsonb,
	"started_at" timestamp,
	"completed_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP
);
--> statement-breakpoint
CREATE TABLE "xos_internal_notes" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"conversation_id" integer,
	"contact_id" integer,
	"ticket_id" integer,
	"user_id" varchar,
	"user_name" varchar(200),
	"content" text NOT NULL,
	"is_pinned" boolean DEFAULT false,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_job_queue" (
	"id" serial PRIMARY KEY NOT NULL,
	"type" text NOT NULL,
	"priority" integer DEFAULT 50,
	"status" text DEFAULT 'pending' NOT NULL,
	"assigned_agent" text,
	"payload" jsonb,
	"result" jsonb,
	"error" text,
	"attempts" integer DEFAULT 0,
	"max_attempts" integer DEFAULT 3,
	"scheduled_at" timestamp,
	"started_at" timestamp,
	"completed_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP,
	"created_by" text DEFAULT 'system',
	"parent_job_id" integer,
	"metadata" jsonb
);
--> statement-breakpoint
CREATE TABLE "xos_messages" (
	"id" serial PRIMARY KEY NOT NULL,
	"conversation_id" integer NOT NULL,
	"direction" varchar(10) NOT NULL,
	"sender_type" varchar(20) NOT NULL,
	"sender_id" varchar,
	"sender_name" varchar(200),
	"content" text,
	"content_type" varchar(30) DEFAULT 'text',
	"attachments" jsonb,
	"metadata" jsonb,
	"external_id" varchar(200),
	"read_at" timestamp,
	"delivered_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_pipeline_stages" (
	"id" serial PRIMARY KEY NOT NULL,
	"pipeline_id" integer NOT NULL,
	"name" varchar(100) NOT NULL,
	"color" varchar(20),
	"sort_order" integer DEFAULT 0,
	"probability" integer DEFAULT 0,
	"is_won" boolean DEFAULT false,
	"is_lost" boolean DEFAULT false,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_pipelines" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"description" text,
	"type" varchar(30) DEFAULT 'sales',
	"is_default" boolean DEFAULT false,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_policy_rules" (
	"id" serial PRIMARY KEY NOT NULL,
	"name" varchar(255) NOT NULL,
	"scope" varchar(50) NOT NULL,
	"target" varchar(255) NOT NULL,
	"effect" varchar(10) NOT NULL,
	"conditions" jsonb,
	"priority" integer DEFAULT 100,
	"description" text,
	"is_active" boolean DEFAULT true NOT NULL,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_queue_users" (
	"id" serial PRIMARY KEY NOT NULL,
	"queue_id" integer NOT NULL,
	"user_id" varchar NOT NULL,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_queues" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"color" varchar(20) DEFAULT 'blue',
	"greeting_message" text,
	"out_of_hours_message" text,
	"schedules" jsonb,
	"order_priority" integer DEFAULT 0,
	"is_active" boolean DEFAULT true,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_quick_messages" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"shortcode" varchar(50) NOT NULL,
	"title" varchar(200),
	"content" text NOT NULL,
	"media_url" text,
	"media_type" varchar(30),
	"scope" varchar(20) DEFAULT 'company',
	"user_id" varchar,
	"usage_count" integer DEFAULT 0,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_scheduled_messages" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"contact_id" integer,
	"conversation_id" integer,
	"content" text NOT NULL,
	"media_url" text,
	"media_type" varchar(30),
	"scheduled_at" timestamp NOT NULL,
	"sent_at" timestamp,
	"status" varchar(20) DEFAULT 'pending',
	"error_message" text,
	"created_by" varchar,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_skill_registry" (
	"id" serial PRIMARY KEY NOT NULL,
	"name" varchar(255) NOT NULL,
	"version" varchar(50) NOT NULL,
	"description" text,
	"steps" jsonb,
	"tools" text[],
	"input_schema" jsonb,
	"output_schema" jsonb,
	"status" varchar(30) DEFAULT 'active',
	"created_by" varchar(100),
	"usage_count" integer DEFAULT 0,
	"success_rate" integer DEFAULT 0,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_staging_changes" (
	"id" serial PRIMARY KEY NOT NULL,
	"pipeline_id" integer,
	"task_id" integer,
	"correlation_id" text,
	"file_path" text NOT NULL,
	"content" text NOT NULL,
	"original_content" text,
	"diff" text,
	"action" text DEFAULT 'create',
	"status" text DEFAULT 'pending' NOT NULL,
	"validation_score" integer,
	"reviewed_by" text,
	"reviewed_at" timestamp,
	"applied_at" timestamp,
	"rolled_back_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP
);
--> statement-breakpoint
CREATE TABLE "xos_tickets" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"contact_id" integer,
	"conversation_id" integer,
	"ticket_number" varchar(20) NOT NULL,
	"subject" varchar(300) NOT NULL,
	"description" text,
	"category" varchar(100),
	"priority" varchar(20) DEFAULT 'normal',
	"status" varchar(30) DEFAULT 'open',
	"assigned_to" varchar,
	"tags" text[],
	"sla_due_at" timestamp,
	"first_response_at" timestamp,
	"resolved_at" timestamp,
	"closed_at" timestamp,
	"satisfaction_score" integer,
	"satisfaction_comment" text,
	"custom_fields" jsonb,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_tool_registry" (
	"id" serial PRIMARY KEY NOT NULL,
	"name" varchar(255) NOT NULL,
	"category" varchar(100),
	"description" text,
	"params_schema" jsonb,
	"version" varchar(50) DEFAULT '1.0.0',
	"allowed_agents" text[],
	"is_active" boolean DEFAULT true NOT NULL,
	"registered_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	CONSTRAINT "xos_tool_registry_name_unique" UNIQUE("name")
);
--> statement-breakpoint
CREATE TABLE "xos_whatsapp_connections" (
	"id" serial PRIMARY KEY NOT NULL,
	"tenant_id" integer,
	"name" varchar(100) NOT NULL,
	"phone_number" varchar(20),
	"status" varchar(30) DEFAULT 'disconnected',
	"qr_code" text,
	"session_data" jsonb,
	"greeting_message" text,
	"farewell_message" text,
	"completion_message" text,
	"rating_message" text,
	"out_of_hours_message" text,
	"ticket_expires_minutes" integer DEFAULT 1440,
	"inactive_message" text,
	"is_default" boolean DEFAULT false,
	"last_seen_at" timestamp,
	"created_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	"updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
--> statement-breakpoint
CREATE TABLE "xos_whatsapp_queue_links" (
	"id" serial PRIMARY KEY NOT NULL,
	"whatsapp_id" integer NOT NULL,
	"queue_id" integer NOT NULL
);
--> statement-breakpoint
CREATE TABLE "retail_trade_ins" (
	"id" serial PRIMARY KEY NOT NULL,
	"seller_id" varchar NOT NULL,
	"company_id" varchar NOT NULL,
	"client_id" varchar NOT NULL,
	"trade_in_date" timestamp NOT NULL,
	"trade_in_value" numeric NOT NULL,
	"status" varchar NOT NULL
);
--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP CONSTRAINT "migration_jobs_user_id_users_id_fk";
--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP CONSTRAINT "migration_jobs_staged_table_id_staged_tables_id_fk";
--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP CONSTRAINT "migration_jobs_mapping_id_staging_mappings_id_fk";
--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP CONSTRAINT "migration_jobs_erp_connection_id_erp_connections_id_fk";
--> statement-breakpoint
ALTER TABLE "migration_jobs" ALTER COLUMN "status" SET DATA TYPE varchar(30);--> statement-breakpoint
ALTER TABLE "migration_jobs" ALTER COLUMN "status" SET DEFAULT 'pending';--> statement-breakpoint
ALTER TABLE "pos_sales" ALTER COLUMN "session_id" DROP NOT NULL;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "person_id" integer;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "power_on" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "power_on_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "screen_issues" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "screen_issues_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "screen_spots" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "screen_spots_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "buttons_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "wear_marks" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "wear_marks_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "wifi_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "wifi_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "sim_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "sim_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "mobile_data_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "mobile_data_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "sensors_nfc_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "sensors_nfc_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "biometric_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "biometric_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "microphones_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "microphones_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "ear_speaker_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "ear_speaker_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "loudspeaker_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "loudspeaker_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "charging_port_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "charging_port_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "cameras_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "cameras_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "flash_working" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "flash_working_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "has_charger" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "has_charger_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "tools_analysis_ok" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "tools_analysis_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "battery_health_notes" text;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "diagnosis_status" varchar(20) DEFAULT 'pending';--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "checklist_data" jsonb;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "linked_service_order_id" integer;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "maintenance_order_id" integer;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "acquisition_value" numeric(12, 2);--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "credit_generated" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "device_evaluations" ADD COLUMN "credit_id" integer;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "name" varchar(200) NOT NULL;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "source_type" varchar(50) NOT NULL;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "source_system" varchar(100);--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "file_name" varchar(500);--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "file_size" integer;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "imported_records" integer DEFAULT 0;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "failed_records" integer DEFAULT 0;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "skipped_records" integer DEFAULT 0;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "analysis_result" jsonb;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "mapping_config" jsonb;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "import_config" jsonb;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "store_id" integer;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "tenant_id" integer;--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "created_by" varchar(255);--> statement-breakpoint
ALTER TABLE "migration_jobs" ADD COLUMN "updated_at" timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL;--> statement-breakpoint
ALTER TABLE "mobile_devices" ADD COLUMN "product_id" integer;--> statement-breakpoint
ALTER TABLE "persons" ADD COLUMN "plus_cliente_id" integer;--> statement-breakpoint
ALTER TABLE "persons" ADD COLUMN "plus_fornecedor_id" integer;--> statement-breakpoint
ALTER TABLE "pos_sales" ADD COLUMN "plus_venda_id" integer;--> statement-breakpoint
ALTER TABLE "pos_sales" ADD COLUMN "plus_nfe_chave" varchar(60);--> statement-breakpoint
ALTER TABLE "pos_sales" ADD COLUMN "plus_sync_status" varchar(20) DEFAULT 'pending';--> statement-breakpoint
ALTER TABLE "pos_sales" ADD COLUMN "plus_sync_error" text;--> statement-breakpoint
ALTER TABLE "pos_sales" ADD COLUMN "plus_synced_at" timestamp;--> statement-breakpoint
ALTER TABLE "pos_sales" ADD COLUMN "empresa_id" integer;--> statement-breakpoint
ALTER TABLE "products" ADD COLUMN "requires_serial_tracking" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "products" ADD COLUMN "tracking_type" varchar(20) DEFAULT 'none';--> statement-breakpoint
ALTER TABLE "products" ADD COLUMN "default_brand" varchar(50);--> statement-breakpoint
ALTER TABLE "products" ADD COLUMN "default_model" varchar(100);--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "store_id" integer;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "description" text;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "type" varchar(30) DEFAULT 'store';--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "is_default" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "responsible_id" varchar;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "is_active" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "allow_negative_stock" boolean DEFAULT false;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "visible_to_all_companies" boolean DEFAULT true;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD COLUMN "updated_at" timestamp DEFAULT CURRENT_TIMESTAMP;--> statement-breakpoint
ALTER TABLE "service_order_items" ADD COLUMN "product_id" integer;--> statement-breakpoint
ALTER TABLE "service_orders" ADD COLUMN "evaluation_status" varchar(30) DEFAULT 'pending';--> statement-breakpoint
ALTER TABLE "service_orders" ADD COLUMN "estimated_value" numeric(12, 2);--> statement-breakpoint
ALTER TABLE "service_orders" ADD COLUMN "evaluated_value" numeric(12, 2);--> statement-breakpoint
ALTER TABLE "service_orders" ADD COLUMN "checklist_data" jsonb;--> statement-breakpoint
ALTER TABLE "service_orders" ADD COLUMN "checklist_completed_at" timestamp;--> statement-breakpoint
ALTER TABLE "service_orders" ADD COLUMN "checklist_completed_by" varchar;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "cnpj" text;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "trade_name" text;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "address" text;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "city" text;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "state" text;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "segment" text;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "notes" text;--> statement-breakpoint
ALTER TABLE "tenants" ADD COLUMN "source" text;--> statement-breakpoint
ALTER TABLE "blackboard_agent_logs" ADD CONSTRAINT "blackboard_agent_logs_task_id_blackboard_tasks_id_fk" FOREIGN KEY ("task_id") REFERENCES "public"."blackboard_tasks"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "blackboard_artifacts" ADD CONSTRAINT "blackboard_artifacts_task_id_blackboard_tasks_id_fk" FOREIGN KEY ("task_id") REFERENCES "public"."blackboard_tasks"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_channels" ADD CONSTRAINT "comm_channels_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_contacts" ADD CONSTRAINT "comm_contacts_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_contacts" ADD CONSTRAINT "comm_contacts_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_events" ADD CONSTRAINT "comm_events_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_messages" ADD CONSTRAINT "comm_messages_thread_id_comm_threads_id_fk" FOREIGN KEY ("thread_id") REFERENCES "public"."comm_threads"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_messages" ADD CONSTRAINT "comm_messages_channel_id_comm_channels_id_fk" FOREIGN KEY ("channel_id") REFERENCES "public"."comm_channels"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_queue_members" ADD CONSTRAINT "comm_queue_members_queue_id_comm_queues_id_fk" FOREIGN KEY ("queue_id") REFERENCES "public"."comm_queues"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_queue_members" ADD CONSTRAINT "comm_queue_members_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_queues" ADD CONSTRAINT "comm_queues_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_quick_messages" ADD CONSTRAINT "comm_quick_messages_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_quick_messages" ADD CONSTRAINT "comm_quick_messages_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_threads" ADD CONSTRAINT "comm_threads_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_threads" ADD CONSTRAINT "comm_threads_contact_id_comm_contacts_id_fk" FOREIGN KEY ("contact_id") REFERENCES "public"."comm_contacts"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_threads" ADD CONSTRAINT "comm_threads_channel_id_comm_channels_id_fk" FOREIGN KEY ("channel_id") REFERENCES "public"."comm_channels"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "comm_threads" ADD CONSTRAINT "comm_threads_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "customer_credits" ADD CONSTRAINT "customer_credits_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "customer_credits" ADD CONSTRAINT "customer_credits_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "etl_migration_jobs" ADD CONSTRAINT "etl_migration_jobs_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "etl_migration_jobs" ADD CONSTRAINT "etl_migration_jobs_staged_table_id_staged_tables_id_fk" FOREIGN KEY ("staged_table_id") REFERENCES "public"."staged_tables"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "etl_migration_jobs" ADD CONSTRAINT "etl_migration_jobs_mapping_id_staging_mappings_id_fk" FOREIGN KEY ("mapping_id") REFERENCES "public"."staging_mappings"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "etl_migration_jobs" ADD CONSTRAINT "etl_migration_jobs_erp_connection_id_erp_connections_id_fk" FOREIGN KEY ("erp_connection_id") REFERENCES "public"."erp_connections"("id") ON DELETE set null ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "migration_logs" ADD CONSTRAINT "migration_logs_job_id_migration_jobs_id_fk" FOREIGN KEY ("job_id") REFERENCES "public"."migration_jobs"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "migration_logs" ADD CONSTRAINT "migration_logs_mapping_id_migration_mappings_id_fk" FOREIGN KEY ("mapping_id") REFERENCES "public"."migration_mappings"("id") ON DELETE set null ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "migration_mappings" ADD CONSTRAINT "migration_mappings_job_id_migration_jobs_id_fk" FOREIGN KEY ("job_id") REFERENCES "public"."migration_jobs"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "module_subscriptions" ADD CONSTRAINT "module_subscriptions_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "module_subscriptions" ADD CONSTRAINT "module_subscriptions_module_id_marketplace_modules_id_fk" FOREIGN KEY ("module_id") REFERENCES "public"."marketplace_modules"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "module_usage" ADD CONSTRAINT "module_usage_subscription_id_module_subscriptions_id_fk" FOREIGN KEY ("subscription_id") REFERENCES "public"."module_subscriptions"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "module_usage" ADD CONSTRAINT "module_usage_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "pos_cash_movements" ADD CONSTRAINT "pos_cash_movements_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "pos_cash_movements" ADD CONSTRAINT "pos_cash_movements_session_id_pos_sessions_id_fk" FOREIGN KEY ("session_id") REFERENCES "public"."pos_sessions"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "pos_cash_movements" ADD CONSTRAINT "pos_cash_movements_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_acquisitions" ADD CONSTRAINT "retail_acquisitions_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_acquisitions" ADD CONSTRAINT "retail_acquisitions_source_evaluation_id_device_evaluations_id_fk" FOREIGN KEY ("source_evaluation_id") REFERENCES "public"."device_evaluations"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_acquisitions" ADD CONSTRAINT "retail_acquisitions_source_service_order_id_service_orders_id_fk" FOREIGN KEY ("source_service_order_id") REFERENCES "public"."service_orders"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_acquisitions" ADD CONSTRAINT "retail_acquisitions_device_id_mobile_devices_id_fk" FOREIGN KEY ("device_id") REFERENCES "public"."mobile_devices"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_acquisitions" ADD CONSTRAINT "retail_acquisitions_linked_device_id_mobile_devices_id_fk" FOREIGN KEY ("linked_device_id") REFERENCES "public"."mobile_devices"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_activity_feed" ADD CONSTRAINT "retail_activity_feed_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_activity_feed" ADD CONSTRAINT "retail_activity_feed_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_commission_closure_items" ADD CONSTRAINT "retail_commission_closure_items_closure_id_retail_commission_closures_id_fk" FOREIGN KEY ("closure_id") REFERENCES "public"."retail_commission_closures"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_commission_closure_items" ADD CONSTRAINT "retail_commission_closure_items_sale_id_pos_sales_id_fk" FOREIGN KEY ("sale_id") REFERENCES "public"."pos_sales"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_commission_closure_items" ADD CONSTRAINT "retail_commission_closure_items_return_id_return_exchanges_id_fk" FOREIGN KEY ("return_id") REFERENCES "public"."return_exchanges"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_commission_closures" ADD CONSTRAINT "retail_commission_closures_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_commission_closures" ADD CONSTRAINT "retail_commission_closures_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_commission_closures" ADD CONSTRAINT "retail_commission_closures_seller_id_retail_sellers_id_fk" FOREIGN KEY ("seller_id") REFERENCES "public"."retail_sellers"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_commission_plans" ADD CONSTRAINT "retail_commission_plans_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_inventories" ADD CONSTRAINT "retail_inventories_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_inventories" ADD CONSTRAINT "retail_inventories_warehouse_id_retail_warehouses_id_fk" FOREIGN KEY ("warehouse_id") REFERENCES "public"."retail_warehouses"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_inventories" ADD CONSTRAINT "retail_inventories_created_by_users_id_fk" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_inventories" ADD CONSTRAINT "retail_inventories_completed_by_users_id_fk" FOREIGN KEY ("completed_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_inventory_items" ADD CONSTRAINT "retail_inventory_items_inventory_id_retail_inventories_id_fk" FOREIGN KEY ("inventory_id") REFERENCES "public"."retail_inventories"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_inventory_items" ADD CONSTRAINT "retail_inventory_items_product_id_products_id_fk" FOREIGN KEY ("product_id") REFERENCES "public"."products"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_inventory_items" ADD CONSTRAINT "retail_inventory_items_counted_by_users_id_fk" FOREIGN KEY ("counted_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_payment_methods" ADD CONSTRAINT "retail_payment_methods_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_price_table_items" ADD CONSTRAINT "retail_price_table_items_price_table_id_retail_price_tables_id_fk" FOREIGN KEY ("price_table_id") REFERENCES "public"."retail_price_tables"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_price_table_items" ADD CONSTRAINT "retail_price_table_items_device_id_mobile_devices_id_fk" FOREIGN KEY ("device_id") REFERENCES "public"."mobile_devices"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_price_tables" ADD CONSTRAINT "retail_price_tables_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_product_serials" ADD CONSTRAINT "retail_product_serials_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_product_serials" ADD CONSTRAINT "retail_product_serials_product_id_products_id_fk" FOREIGN KEY ("product_id") REFERENCES "public"."products"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_product_serials" ADD CONSTRAINT "retail_product_serials_warehouse_id_retail_warehouses_id_fk" FOREIGN KEY ("warehouse_id") REFERENCES "public"."retail_warehouses"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_product_serials" ADD CONSTRAINT "retail_product_serials_movement_id_retail_stock_movements_id_fk" FOREIGN KEY ("movement_id") REFERENCES "public"."retail_stock_movements"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_product_serials" ADD CONSTRAINT "retail_product_serials_customer_id_persons_id_fk" FOREIGN KEY ("customer_id") REFERENCES "public"."persons"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_product_types" ADD CONSTRAINT "retail_product_types_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_product_types" ADD CONSTRAINT "retail_product_types_tax_group_id_fiscal_grupos_tributacao_id_fk" FOREIGN KEY ("tax_group_id") REFERENCES "public"."fiscal_grupos_tributacao"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_promotions" ADD CONSTRAINT "retail_promotions_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_promotions" ADD CONSTRAINT "retail_promotions_price_table_id_retail_price_tables_id_fk" FOREIGN KEY ("price_table_id") REFERENCES "public"."retail_price_tables"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_reports" ADD CONSTRAINT "retail_reports_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_seller_goals" ADD CONSTRAINT "retail_seller_goals_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_seller_goals" ADD CONSTRAINT "retail_seller_goals_seller_id_retail_sellers_id_fk" FOREIGN KEY ("seller_id") REFERENCES "public"."retail_sellers"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_seller_goals" ADD CONSTRAINT "retail_seller_goals_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_sellers" ADD CONSTRAINT "retail_sellers_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_sellers" ADD CONSTRAINT "retail_sellers_person_id_persons_id_fk" FOREIGN KEY ("person_id") REFERENCES "public"."persons"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_sellers" ADD CONSTRAINT "retail_sellers_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_movements" ADD CONSTRAINT "retail_stock_movements_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_movements" ADD CONSTRAINT "retail_stock_movements_warehouse_id_retail_warehouses_id_fk" FOREIGN KEY ("warehouse_id") REFERENCES "public"."retail_warehouses"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_movements" ADD CONSTRAINT "retail_stock_movements_product_id_products_id_fk" FOREIGN KEY ("product_id") REFERENCES "public"."products"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_movements" ADD CONSTRAINT "retail_stock_movements_supplier_id_persons_id_fk" FOREIGN KEY ("supplier_id") REFERENCES "public"."persons"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_movements" ADD CONSTRAINT "retail_stock_movements_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfer_items" ADD CONSTRAINT "retail_stock_transfer_items_transfer_id_retail_stock_transfers_id_fk" FOREIGN KEY ("transfer_id") REFERENCES "public"."retail_stock_transfers"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfer_items" ADD CONSTRAINT "retail_stock_transfer_items_product_id_products_id_fk" FOREIGN KEY ("product_id") REFERENCES "public"."products"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfers" ADD CONSTRAINT "retail_stock_transfers_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfers" ADD CONSTRAINT "retail_stock_transfers_source_warehouse_id_retail_warehouses_id_fk" FOREIGN KEY ("source_warehouse_id") REFERENCES "public"."retail_warehouses"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfers" ADD CONSTRAINT "retail_stock_transfers_destination_warehouse_id_retail_warehouses_id_fk" FOREIGN KEY ("destination_warehouse_id") REFERENCES "public"."retail_warehouses"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfers" ADD CONSTRAINT "retail_stock_transfers_requested_by_users_id_fk" FOREIGN KEY ("requested_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfers" ADD CONSTRAINT "retail_stock_transfers_approved_by_users_id_fk" FOREIGN KEY ("approved_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_stock_transfers" ADD CONSTRAINT "retail_stock_transfers_completed_by_users_id_fk" FOREIGN KEY ("completed_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_store_goals" ADD CONSTRAINT "retail_store_goals_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_store_goals" ADD CONSTRAINT "retail_store_goals_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_transfer_serials" ADD CONSTRAINT "retail_transfer_serials_transfer_item_id_retail_stock_transfer_items_id_fk" FOREIGN KEY ("transfer_item_id") REFERENCES "public"."retail_stock_transfer_items"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_transfer_serials" ADD CONSTRAINT "retail_transfer_serials_serial_id_retail_product_serials_id_fk" FOREIGN KEY ("serial_id") REFERENCES "public"."retail_product_serials"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_warehouse_stock" ADD CONSTRAINT "retail_warehouse_stock_warehouse_id_retail_warehouses_id_fk" FOREIGN KEY ("warehouse_id") REFERENCES "public"."retail_warehouses"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_warehouse_stock" ADD CONSTRAINT "retail_warehouse_stock_product_id_products_id_fk" FOREIGN KEY ("product_id") REFERENCES "public"."products"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "service_warranties" ADD CONSTRAINT "service_warranties_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "service_warranties" ADD CONSTRAINT "service_warranties_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "service_warranties" ADD CONSTRAINT "service_warranties_service_order_id_service_orders_id_fk" FOREIGN KEY ("service_order_id") REFERENCES "public"."service_orders"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "service_warranties" ADD CONSTRAINT "service_warranties_device_id_mobile_devices_id_fk" FOREIGN KEY ("device_id") REFERENCES "public"."mobile_devices"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "tenant_empresas" ADD CONSTRAINT "tenant_empresas_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_activities" ADD CONSTRAINT "xos_activities_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_activities" ADD CONSTRAINT "xos_activities_contact_id_xos_contacts_id_fk" FOREIGN KEY ("contact_id") REFERENCES "public"."xos_contacts"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_activities" ADD CONSTRAINT "xos_activities_company_id_xos_companies_id_fk" FOREIGN KEY ("company_id") REFERENCES "public"."xos_companies"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_activities" ADD CONSTRAINT "xos_activities_deal_id_xos_deals_id_fk" FOREIGN KEY ("deal_id") REFERENCES "public"."xos_deals"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_activities" ADD CONSTRAINT "xos_activities_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_activities" ADD CONSTRAINT "xos_activities_created_by_users_id_fk" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_automations" ADD CONSTRAINT "xos_automations_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_automations" ADD CONSTRAINT "xos_automations_created_by_users_id_fk" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_campaigns" ADD CONSTRAINT "xos_campaigns_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_campaigns" ADD CONSTRAINT "xos_campaigns_created_by_users_id_fk" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_companies" ADD CONSTRAINT "xos_companies_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_companies" ADD CONSTRAINT "xos_companies_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_contacts" ADD CONSTRAINT "xos_contacts_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_contacts" ADD CONSTRAINT "xos_contacts_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_conversation_tracking" ADD CONSTRAINT "xos_conversation_tracking_conversation_id_xos_conversations_id_fk" FOREIGN KEY ("conversation_id") REFERENCES "public"."xos_conversations"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_conversation_tracking" ADD CONSTRAINT "xos_conversation_tracking_queue_id_xos_queues_id_fk" FOREIGN KEY ("queue_id") REFERENCES "public"."xos_queues"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_conversations" ADD CONSTRAINT "xos_conversations_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_conversations" ADD CONSTRAINT "xos_conversations_contact_id_xos_contacts_id_fk" FOREIGN KEY ("contact_id") REFERENCES "public"."xos_contacts"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_conversations" ADD CONSTRAINT "xos_conversations_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_deals" ADD CONSTRAINT "xos_deals_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_deals" ADD CONSTRAINT "xos_deals_pipeline_id_xos_pipelines_id_fk" FOREIGN KEY ("pipeline_id") REFERENCES "public"."xos_pipelines"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_deals" ADD CONSTRAINT "xos_deals_stage_id_xos_pipeline_stages_id_fk" FOREIGN KEY ("stage_id") REFERENCES "public"."xos_pipeline_stages"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_deals" ADD CONSTRAINT "xos_deals_contact_id_xos_contacts_id_fk" FOREIGN KEY ("contact_id") REFERENCES "public"."xos_contacts"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_deals" ADD CONSTRAINT "xos_deals_company_id_xos_companies_id_fk" FOREIGN KEY ("company_id") REFERENCES "public"."xos_companies"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_deals" ADD CONSTRAINT "xos_deals_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_internal_notes" ADD CONSTRAINT "xos_internal_notes_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_internal_notes" ADD CONSTRAINT "xos_internal_notes_conversation_id_xos_conversations_id_fk" FOREIGN KEY ("conversation_id") REFERENCES "public"."xos_conversations"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_internal_notes" ADD CONSTRAINT "xos_internal_notes_contact_id_xos_contacts_id_fk" FOREIGN KEY ("contact_id") REFERENCES "public"."xos_contacts"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_internal_notes" ADD CONSTRAINT "xos_internal_notes_ticket_id_xos_tickets_id_fk" FOREIGN KEY ("ticket_id") REFERENCES "public"."xos_tickets"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_internal_notes" ADD CONSTRAINT "xos_internal_notes_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_messages" ADD CONSTRAINT "xos_messages_conversation_id_xos_conversations_id_fk" FOREIGN KEY ("conversation_id") REFERENCES "public"."xos_conversations"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_pipeline_stages" ADD CONSTRAINT "xos_pipeline_stages_pipeline_id_xos_pipelines_id_fk" FOREIGN KEY ("pipeline_id") REFERENCES "public"."xos_pipelines"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_pipelines" ADD CONSTRAINT "xos_pipelines_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_queue_users" ADD CONSTRAINT "xos_queue_users_queue_id_xos_queues_id_fk" FOREIGN KEY ("queue_id") REFERENCES "public"."xos_queues"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_queue_users" ADD CONSTRAINT "xos_queue_users_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_queues" ADD CONSTRAINT "xos_queues_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_quick_messages" ADD CONSTRAINT "xos_quick_messages_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_quick_messages" ADD CONSTRAINT "xos_quick_messages_user_id_users_id_fk" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_scheduled_messages" ADD CONSTRAINT "xos_scheduled_messages_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_scheduled_messages" ADD CONSTRAINT "xos_scheduled_messages_contact_id_xos_contacts_id_fk" FOREIGN KEY ("contact_id") REFERENCES "public"."xos_contacts"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_scheduled_messages" ADD CONSTRAINT "xos_scheduled_messages_conversation_id_xos_conversations_id_fk" FOREIGN KEY ("conversation_id") REFERENCES "public"."xos_conversations"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_scheduled_messages" ADD CONSTRAINT "xos_scheduled_messages_created_by_users_id_fk" FOREIGN KEY ("created_by") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_tickets" ADD CONSTRAINT "xos_tickets_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_tickets" ADD CONSTRAINT "xos_tickets_contact_id_xos_contacts_id_fk" FOREIGN KEY ("contact_id") REFERENCES "public"."xos_contacts"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_tickets" ADD CONSTRAINT "xos_tickets_conversation_id_xos_conversations_id_fk" FOREIGN KEY ("conversation_id") REFERENCES "public"."xos_conversations"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_tickets" ADD CONSTRAINT "xos_tickets_assigned_to_users_id_fk" FOREIGN KEY ("assigned_to") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_whatsapp_connections" ADD CONSTRAINT "xos_whatsapp_connections_tenant_id_tenants_id_fk" FOREIGN KEY ("tenant_id") REFERENCES "public"."tenants"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_whatsapp_queue_links" ADD CONSTRAINT "xos_whatsapp_queue_links_whatsapp_id_xos_whatsapp_connections_id_fk" FOREIGN KEY ("whatsapp_id") REFERENCES "public"."xos_whatsapp_connections"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "xos_whatsapp_queue_links" ADD CONSTRAINT "xos_whatsapp_queue_links_queue_id_xos_queues_id_fk" FOREIGN KEY ("queue_id") REFERENCES "public"."xos_queues"("id") ON DELETE cascade ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "mobile_devices" ADD CONSTRAINT "mobile_devices_product_id_products_id_fk" FOREIGN KEY ("product_id") REFERENCES "public"."products"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "pos_sales" ADD CONSTRAINT "pos_sales_empresa_id_tenant_empresas_id_fk" FOREIGN KEY ("empresa_id") REFERENCES "public"."tenant_empresas"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD CONSTRAINT "retail_warehouses_store_id_retail_stores_id_fk" FOREIGN KEY ("store_id") REFERENCES "public"."retail_stores"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "retail_warehouses" ADD CONSTRAINT "retail_warehouses_responsible_id_users_id_fk" FOREIGN KEY ("responsible_id") REFERENCES "public"."users"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "service_order_items" ADD CONSTRAINT "service_order_items_product_id_products_id_fk" FOREIGN KEY ("product_id") REFERENCES "public"."products"("id") ON DELETE no action ON UPDATE no action;--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "screen_notes";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "body_notes";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "charger_included";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "charger_condition";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "battery_notes";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "camera_front_working";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "camera_rear_working";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "audio_working";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "connectivity_working";--> statement-breakpoint
ALTER TABLE "device_evaluations" DROP COLUMN "water_damage_detected";--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP COLUMN "user_id";--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP COLUMN "staged_table_id";--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP COLUMN "mapping_id";--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP COLUMN "erp_connection_id";--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP COLUMN "processed_records";--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP COLUMN "success_records";--> statement-breakpoint
ALTER TABLE "migration_jobs" DROP COLUMN "error_records";