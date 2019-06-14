require 'rails_helper'

module Tickets
  RSpec.describe Ticket, type: :model, tickets: true do
    it_behaves_like 'localized model'
    it { expect(build :ticket, user: build(:user)).to be_valid }
    it { expect(build :ticket, position: build(:position)).to be_valid }
    it { expect(build :ticket).not_to be_valid }
    it { should validate_presence_of :name }
    it { should validate_presence_of :company }
    it { should belong_to :author }
    it { should belong_to :position }
    it { should belong_to :user }
    it { should have_many :checklist_items }
    it { should accept_nested_attributes_for :checklist_items }
    it { should have_many :messages }
    it { should belong_to :company }
    it { should have_many :tickets }
    it { should belong_to :ticket }
    it { should belong_to :ticketable }
    
    it "adds messages" do
      ticket = create(:ticket, user: create(:user))
      expect { ticket.add_log(text: 'test') }.to change(Message, :count).by (1)
    end
    # Видимость
    it "visible by user" do
      user = create(:manager_with_company, permissions: [])
      company = user.employers.first
      expect(build(:ticket, author: user, company: company).visible_by?(user)).to be_truthy
      expect(build(:ticket, position: user.positions.first, company: company).visible_by?(user)).to be_truthy
      expect(build(:ticket, user: user, company: company).visible_by?(user)).to be_truthy
      expect(build(:ticket, company: company).visible_by?(user)).to be_falsey
    end

    it "visible by viewer" do
      user = create(:manager_with_company, permissions: [:view_tickets])
      company = user.employers.first
      expect(build(:ticket, company: company).visible_by?(user)).to be_truthy
      expect(build(:ticket).visible_by?(user)).to be_falsey
    end
    
    # Управление
    it "manageable by user" do
      user = create(:manager_with_company, permissions: [])
      company = user.employers.first
      expect(build(:ticket, author: user, company: company).manageable_by?(user)).to be_truthy
      expect(build(:ticket, position: user.positions.first, company: company).manageable_by?(user)).to be_truthy
      expect(build(:ticket, user: user, company: company).manageable_by?(user)).to be_truthy
      expect(build(:ticket, company: company).manageable_by?(user)).to be_falsey
    end

    it "manageable and visible by manager" do
      user = create(:manager_with_company, permissions: [:manage_tickets])
      company = user.employers.first
      expect(build(:ticket, company: company).visible_by?(user)).to be_truthy
      expect(build(:ticket, company: company).manageable_by?(user)).to be_truthy
    end

    # Состояния
    it "is created as new" do
      expect(create(:ticket, position: create(:position)).state).to eq('new')
    end

    it "is expired" do
      ticket = build(:ticket, end_on: Date.yesterday)
      expect(ticket.expired?).to eq(true)
    end

    it "closes after children closes" do
      user = create(:user)
      company = create(:company)
      h = { author: user, user: user, company: company, state: 'in_progress' }
      ticket = create(:ticket, h)
      ticket_1 = create(:ticket, {ticket: ticket}.merge(h))
      ticket_2 = create(:ticket, {ticket: ticket}.merge(h))
      ticket_1.finish
      expect(ticket.reload.state).to eq('in_progress')
      ticket_2.finish
      expect(ticket.reload.state).to eq('complete')
    end
  end
end