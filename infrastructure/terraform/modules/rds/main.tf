variable "environment" {
  type = string
}

variable "vpc_id" {
  type = string
}

variable "private_subnet_ids" {
  type = list(string)
}

variable "instance_class" {
  type    = string
  default = "db.t4g.small"
}

variable "allocated_storage" {
  type    = number
  default = 20
}

variable "engine_version" {
  type    = string
  default = "18"
}

variable "database_name" {
  type = string
}

variable "master_username" {
  type = string
}

variable "master_password" {
  type      = string
  sensitive = true
}

variable "vpc_cidr_block" {
  description = "CIDR block of the VPC"
  type        = string
}

resource "aws_db_subnet_group" "main" {
  name       = "archtech-${var.environment}-rds-subnets"
  subnet_ids = var.private_subnet_ids

  tags = {
    Name        = "archtech-${var.environment}-rds-subnets"
    Environment = var.environment
  }
}

resource "aws_security_group" "rds" {
  name        = "archtech-${var.environment}-rds-sg"
  description = "Security group for RDS PostgreSQL"
  vpc_id      = var.vpc_id

  ingress {
    from_port       = 5432
    to_port         = 5432
    protocol        = "tcp"
    cidr_blocks     = [var.vpc_cidr_block]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "archtech-${var.environment}-rds-sg"
    Environment = var.environment
  }
}

resource "aws_db_instance" "main" {
  identifier = "archtech-${var.environment}"
  engine     = "postgres"
  engine_version = var.engine_version

  instance_class    = var.instance_class
  allocated_storage = var.allocated_storage
  storage_type      = "gp3"
  storage_encrypted = true

  db_name  = var.database_name
  username = var.master_username
  password = var.master_password

  db_subnet_group_name   = aws_db_subnet_group.main.name
  vpc_security_group_ids = [aws_security_group.rds.id]

  backup_retention_period = 30
  backup_window           = "03:00-04:00"
  maintenance_window      = "sun:05:00-sun:06:00"

  deletion_protection = true
  skip_final_snapshot = false
  final_snapshot_identifier = "archtech-${var.environment}-final-${formatdate("YYYY-MM-DD-hhmm", timestamp())}"

  enabled_cloudwatch_logs_exports = ["postgresql"]

  tags = {
    Name        = "archtech-${var.environment}-rds"
    Environment = var.environment
    ManagedBy   = "terraform"
  }
}

resource "aws_ssm_parameter" "rds_connection_string" {
  name  = "/archtech/${var.environment}/rds/connection_string"
  type  = "SecureString"
  value = "pgsql://${var.master_username}:${var.master_password}@${aws_db_instance.main.endpoint}/${var.database_name}"
}

output "endpoint" {
  value = aws_db_instance.main.endpoint
}

output "database_name" {
  value = aws_db_instance.main.db_name
}

output "master_username" {
  value = aws_db_instance.main.username
}

output "security_group_id" {
  value = aws_security_group.rds.id
}
