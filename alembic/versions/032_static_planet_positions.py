"""add static planet positions

Revision ID: 032_static_planet_positions
Revises: 031_gate_warp_routing
Create Date: 2026-03-31
"""

from alembic import op
import sqlalchemy as sa


revision = "032_static_planet_positions"
down_revision = "031_gate_warp_routing"
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.add_column("static_planets", sa.Column("x", sa.Float(), nullable=True))
    op.add_column("static_planets", sa.Column("y", sa.Float(), nullable=True))
    op.add_column("static_planets", sa.Column("z", sa.Float(), nullable=True))


def downgrade() -> None:
    op.drop_column("static_planets", "z")
    op.drop_column("static_planets", "y")
    op.drop_column("static_planets", "x")
