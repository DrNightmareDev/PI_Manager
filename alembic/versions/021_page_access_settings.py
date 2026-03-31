"""Add page access settings

Revision ID: 021_page_access_settings
Revises: 020_corp_bridge_connections
Create Date: 2026-03-31 13:20:00.000000
"""

from alembic import op
import sqlalchemy as sa


revision = "021_page_access_settings"
down_revision = "020_corp_bridge_connections"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        "page_access_settings",
        sa.Column("page_key", sa.String(length=100), nullable=False),
        sa.Column("access_level", sa.String(length=20), nullable=False, server_default="member"),
        sa.Column("updated_at", sa.DateTime(timezone=True), server_default=sa.func.now(), nullable=True),
        sa.PrimaryKeyConstraint("page_key"),
    )

    op.bulk_insert(
        sa.table(
            "page_access_settings",
            sa.column("page_key", sa.String),
            sa.column("access_level", sa.String),
        ),
        [
            {"page_key": "dashboard", "access_level": "member"},
            {"page_key": "dashboard_characters", "access_level": "member"},
            {"page_key": "dashboard_corp", "access_level": "manager"},
            {"page_key": "skyhook", "access_level": "member"},
            {"page_key": "planner", "access_level": "member"},
            {"page_key": "colony_plan", "access_level": "member"},
            {"page_key": "pi_templates", "access_level": "member"},
            {"page_key": "hauling", "access_level": "manager"},
            {"page_key": "system", "access_level": "member"},
            {"page_key": "market", "access_level": "member"},
            {"page_key": "manager", "access_level": "manager"},
        ],
    )


def downgrade() -> None:
    op.drop_table("page_access_settings")
