terraform {
  backend "s3" {
    bucket         = "archtech-terraform-state"
    key            = "dev/terraform.tfstate"
    region         = "us-east-1"
    encrypt        = true
    dynamodb_table = "archtech-terraform-locks"
  }

  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  region = "us-east-1"
}

module "vpc" {
  source = "../../modules/vpc"

  environment = "dev"
  vpc_cidr   = "10.0.0.0/16"
}

module "eks" {
  source = "../../modules/eks"

  environment       = "dev"
  vpc_id            = module.vpc.vpc_id
  private_subnet_ids = module.vpc.private_subnet_ids
  public_subnet_ids  = module.vpc.public_subnet_ids
  node_instance_types = ["t3.medium"]
  min_nodes          = 1
  max_nodes          = 3
  desired_nodes      = 1
}

module "rds" {
  source = "../../modules/rds"

  environment        = "dev"
  vpc_id             = module.vpc.vpc_id
  vpc_cidr_block     = module.vpc.vpc_cidr_block
  private_subnet_ids = module.vpc.private_subnet_ids
  instance_class     = "db.t4g.small"
  allocated_storage  = 20
  engine_version     = "18"
  database_name      = "archtech"
  master_username    = "archtech"
  master_password    = data.aws_secretsmanager_secret_version.rds_password.secret_string
}

module "redis" {
  source = "../../modules/redis"

  environment        = "dev"
  vpc_id             = module.vpc.vpc_id
  private_subnet_ids = module.vpc.private_subnet_ids
  node_type          = "cache.t4g.small"
  num_cache_nodes    = 1
}

data "aws_secretsmanager_secret_version" "rds_password" {
  secret_id = "archtech/dev/rds"
}
