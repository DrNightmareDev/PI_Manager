"""Add pi_favorites table

Revision ID: 004
Revises: 003
Create Date: 2026-03-22 00:00:00.000000
"""
from typing import Sequence, Union
from alembic import op
import sqlalchemy as sa

revision: str = '004'
down_revision: Union[str, None] = '003'
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade() -> None:
    op.create_table(
        'pi_favorites',
        sa.Column('id', sa.Integer(), primary_key=True),
        sa.Column('account_id', sa.Integer(), sa.ForeignKey('accounts.id', ondelete='CASCADE'), nullable=False),
        sa.Column('product_name', sa.String(255), nullable=False),
        sa.Column('created_at', sa.DateTime(timezone=True), server_default=sa.func.now()),
    )
    op.create_index('ix_pi_favorites_account_id', 'pi_favorites', ['account_id'])
    op.create_unique_constraint('uq_pi_favorites_account_product', 'pi_favorites', ['account_id', 'product_name'])


def downgrade() -> None:
    op.drop_table('pi_favorites')
