/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Main.java to edit this template
 */
package hospitalmanagementsystem;

/**
 *
 * @author munei
 */;

import java.sql.*;
import java.util.Scanner;

public class HospitalManagementSystem {
    // Database connection details
    static final String URL = "jdbc:mysql://localhost:3306/HospitalDB";
    static final String USER = "root"; 
    static final String PASS = "9501Chris#"; // replace with your actual password

    static Connection conn;
    static Scanner sc = new Scanner(System.in);

    public static void main(String[] args) {
  
    System.out.println("Program started!");

    try {
        // Try loading MySQL driver manually
        Class.forName("com.mysql.cj.jdbc.Driver");
        System.out.println("MySQL JDBC Driver loaded successfully!");

        // Now connect
        conn = DriverManager.getConnection(
            "jdbc:mysql://localhost:3306/HospitalDB", 
            "root", 
            "9501Chris#"
        );
        System.out.println("Connected to HospitalDB successfully!");

        // ... rest of your menu code here ...


            int choice;
            do {
                System.out.println("Patient Database Menu");
                System.out.println("1. View Patients");
                System.out.println("2. Add Patient");
                System.out.println("3. Delete Patient");
                System.out.println("4. Exit");
                System.out.print("Select an option: ");
                choice = sc.nextInt();
                sc.nextLine(); // consume newline

                switch (choice) {
                    case 1 -> viewPatients();
                    case 2 -> addPatient();
                    case 3 -> deletePatient();
                    case 4 -> System.out.println("Exiting program...");
                    default -> System.out.println("Invalid choice. Try again!");
                }
            } while (choice != 4);

            conn.close();
        } catch (ClassNotFoundException e) {
            System.out.println(" MySQL JDBC Driver not found. Please add it to your project libraries.");
        } catch (SQLException e) {
            System.out.println("Database connection error:");
            e.printStackTrace();
        }
    }

    static void viewPatients() throws SQLException {
        String sql = "SELECT * FROM Patient";
        try (Statement stmt = conn.createStatement(); ResultSet rs = stmt.executeQuery(sql)) {
            System.out.println("Patient Records");
            boolean hasRecords = false;
            while (rs.next()) {
                hasRecords = true;
                System.out.printf("%d | %s | %s | %s%n",
                        rs.getInt("patient_id"),
                        rs.getString("first_name"),
                        rs.getString("last_name"),
                        rs.getString("address"));
            }
            if (!hasRecords) {
                System.out.println("No patient records found.");
            }
        }
    }

    static void addPatient() throws SQLException {
        System.out.print("Enter first name: ");
        String firstName = sc.nextLine();
        System.out.print("Enter last name: ");
        String lastName = sc.nextLine();
        System.out.print("Enter address: ");
        String address = sc.nextLine();

        if (firstName.isEmpty() || lastName.isEmpty() || address.isEmpty()) {
            System.out.println("Please fill in all fields.");
            return;
        }

        String sql = "INSERT INTO Patient (first_name, last_name, address) VALUES (?, ?, ?)";
        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setString(1, firstName);
            pstmt.setString(2, lastName);
            pstmt.setString(3, address);
            int rows = pstmt.executeUpdate();
            if (rows > 0) {
                System.out.println("Patient added successfully!");
            }
        }
    }

    static void deletePatient() throws SQLException {
        System.out.print("Enter patient ID to delete: ");
        int id = sc.nextInt();
        sc.nextLine(); // consume newline

        String sql = "DELETE FROM Patient WHERE patient_id = ?";
        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, id);
            int rows = pstmt.executeUpdate();
            if (rows > 0) {
                System.out.println("Patient deleted successfully!");
            } else {
                System.out.println(" Patient not found.");
            }
        }
    }
}
