variable "environment" {
  type = string
}

variable "vpc_id" {
  type = string
}

variable "private_subnet_ids" {
  type = list(string)
}

variable "node_type" {
  type    = string
  default = "cache.t4g.small"
}

variable "num_cache_nodes" {
  type    = number
  default = 1
}

resource "aws_security_group" "redis" {
  name        = "archtech-${var.environment}-redis-sg"
  description = "Security group for ElastiCache Redis"
  vpc_id      = var.vpc_id

  ingress {
    from_port       = 6379
    to_port         = 6379
    protocol        = "tcp"
    cidr_blocks     = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "archtech-${var.environment}-redis-sg"
    Environment = var.environment
  }
}

resource "aws_elasticache_subnet_group" "main" {
  name       = "archtech-${var.environment}-redis-subnets"
  subnet_ids = var.private_subnet_ids
}

resource "aws_elasticache_cluster" "main" {
  cluster_id           = "archtech-${var.environment}"
  engine               = "redis"
  engine_version       = "7.1"
  node_type            = var.node_type
  num_cache_nodes      = var.num_cache_nodes
  parameter_group_name = "default.redis7"
  port                 = 6379

  subnet_group_name  = aws_elasticache_subnet_group.main.name
  security_group_ids = [aws_security_group.redis.id]

  tags = {
    Name        = "archtech-${var.environment}-redis"
    Environment = var.environment
    ManagedBy   = "terraform"
  }
}

output "endpoint" {
  value = aws_elasticache_cluster.main.cache_nodes[0].address
}

output "port" {
  value = aws_elasticache_cluster.main.cache_nodes[0].port
}

output "security_group_id" {
  value = aws_security_group.redis.id
}
