/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 */

package com.mycompany.sportsselectionsystem;

/**
 *
 * @author HP
 */
//
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;

public class SportsSelectionSystem extends JFrame {
    private static final String[] INDIVIDUAL_SPORTS = {"Chess", "Boxing", "Figure Skating"};
    private static final String[] PARTNER_SPORTS = {"Table Tennis", "Badminton"};
    private static final String[] TEAM_SPORTS = {"Soccer", "Rugby", "Hockey"};
    private static final int STAFF_COUNT = 10;

    private JPanel staffPanel;
    private JLabel summaryLabel;
    private java.util.List<StaffSelectionPanel> staffSelections;

    public SportsSelectionSystem() {
        setTitle("Sports Selection System");
        setSize(600, 600);
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setLocationRelativeTo(null);

        staffSelections = new ArrayList<>();
        staffPanel = new JPanel();
        staffPanel.setLayout(new GridLayout(STAFF_COUNT, 1, 5, 5));

        for (int i = 1; i <= STAFF_COUNT; i++) {
            StaffSelectionPanel panel = new StaffSelectionPanel("Staff " + i);
            staffSelections.add(panel);
            staffPanel.add(panel);
        }

        JButton summaryButton = new JButton("Show Summary");
        summaryButton.addActionListener(e -> showSummary());

        summaryLabel = new JLabel("Total selections will appear here.");
        summaryLabel.setFont(new Font("Arial", Font.BOLD, 16));
        summaryLabel.setBorder(BorderFactory.createEmptyBorder(10, 0, 0, 0));

        JPanel bottomPanel = new JPanel();
        bottomPanel.setLayout(new BorderLayout());
        bottomPanel.add(summaryButton, BorderLayout.NORTH);
        bottomPanel.add(summaryLabel, BorderLayout.CENTER);

        add(new JScrollPane(staffPanel), BorderLayout.CENTER);
        add(bottomPanel, BorderLayout.SOUTH);
    }

    private void showSummary() {
        int individualCount = 0, partnerCount = 0, teamCount = 0;
        for (StaffSelectionPanel panel : staffSelections) {
            individualCount += panel.getIndividualSelections();
            partnerCount += panel.getPartnerSelections();
            teamCount += panel.getTeamSelections();
        }
        summaryLabel.setText(String.format(
            "Summary: Individual: %d | Partner: %d | Team: %d",
            individualCount, partnerCount, teamCount
        ));
    }

    // Inner class for each staff member's sport selection
    static class StaffSelectionPanel extends JPanel {
        JCheckBox[] individualBoxes, partnerBoxes, teamBoxes;

        StaffSelectionPanel(String staffName) {
            setLayout(new FlowLayout(FlowLayout.LEFT));
            add(new JLabel(staffName + ": "));

            individualBoxes = createCheckBoxes(INDIVIDUAL_SPORTS);
            partnerBoxes = createCheckBoxes(PARTNER_SPORTS);
            teamBoxes = createCheckBoxes(TEAM_SPORTS);

            add(new JLabel("Individual:"));
            for (JCheckBox box : individualBoxes) add(box);

            add(new JLabel("Partner:"));
            for (JCheckBox box : partnerBoxes) add(box);

            add(new JLabel("Team:"));
            for (JCheckBox box : teamBoxes) add(box);
        }

        private JCheckBox[] createCheckBoxes(String[] sports) {
            JCheckBox[] boxes = new JCheckBox[sports.length];
            for (int i = 0; i < sports.length; i++) {
                boxes[i] = new JCheckBox(sports[i]);
            }
            return boxes;
        }

        public int getIndividualSelections() {
            return countSelected(individualBoxes);
        }
        public int getPartnerSelections() {
            return countSelected(partnerBoxes);
        }
        public int getTeamSelections() {
            return countSelected(teamBoxes);
        }

        private int countSelected(JCheckBox[] boxes) {
            int count = 0;
            for (JCheckBox box : boxes) {
                if (box.isSelected()) count++;
            }
            return count;
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new SportsSelectionSystem().setVisible(true));
    }
}
